<?php

namespace App\Livewire\Admin\Site;

use App\Models\SiteContentEntry;
use App\Services\Cloudinary;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

/**
 * Edit one home page section's photos and copy. Nothing reaches the site until
 * Save; a field saved as its default is removed, so the default shows again.
 */
class Edit extends Component
{
    use WithFileUploads;

    public string $section;

    /** @var array<string, string> Text fields, keyed by field name. */
    public array $values = [];

    /** @var array<string, string> Descriptions of uploaded photos, keyed by field name. */
    public array $alts = [];

    /** @var array<string, UploadedFile|null> New photos waiting to be saved. */
    public array $photos = [];

    /** @var array<string, bool> Uploaded photos to swap back to the original on save. */
    public array $restore = [];

    /** Bumped on save so the live preview reloads. */
    public int $version = 0;

    public function mount(string $section, SiteContent $content): void
    {
        abort_unless(array_key_exists($section, $content->sections()), 404);

        $this->section = $section;
        $this->fillFromContent($content);
    }

    protected function fillFromContent(SiteContent $content): void
    {
        $this->values = [];
        $this->alts = [];

        foreach ($content->fields($this->section) as $key => $field) {
            $name = $this->name($key);

            if ($field['type'] === 'image') {
                $this->alts[$name] = $content->image($key) !== null ? $content->alt($key) : '';
            } else {
                $this->values[$name] = $content->text($key);
            }
        }

        $this->photos = [];
        $this->restore = [];
    }

    /**
     * Put a text field back to its default. It is saved with the rest.
     */
    public function useDefault(string $name, SiteContent $content): void
    {
        $field = $content->field($this->key($name));

        if ($field['type'] !== 'image') {
            $this->values[$name] = (string) $field['default'];
        }
    }

    /**
     * Swap an uploaded photo back to the original on the next save.
     */
    public function restorePhoto(string $name): void
    {
        unset($this->photos[$name]);
        $this->restore[$name] = true;
    }

    public function undoRestore(string $name): void
    {
        unset($this->restore[$name]);
    }

    public function discardPhoto(string $name): void
    {
        unset($this->photos[$name]);
    }

    /**
     * A new photo replaces any pending restore of the same slot.
     */
    public function updatedPhotos(mixed $value, string $name): void
    {
        unset($this->restore[$name]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $rules = [];

        foreach (app(SiteContent::class)->fields($this->section) as $key => $field) {
            $name = $this->name($key);

            $rules += match ($field['type']) {
                'image' => [
                    "photos.{$name}" => ['nullable', 'image', 'max:5120'],
                    "alts.{$name}" => ['nullable', 'string', 'max:255'],
                ],
                'text' => ["values.{$name}" => ['nullable', 'string', 'max:255']],
                default => ["values.{$name}" => ['nullable', 'string', 'max:3000']],
            };
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        $attributes = [];

        foreach (app(SiteContent::class)->fields($this->section) as $key => $field) {
            $name = $this->name($key);
            $attributes[$field['type'] === 'image' ? "photos.{$name}" : "values.{$name}"] = strtolower((string) $field['label']);
            $attributes["alts.{$name}"] = 'description';
        }

        return $attributes;
    }

    public function save(SiteContent $content, Cloudinary $cloudinary): void
    {
        $this->validate();

        $fields = $content->fields($this->section);
        $existing = SiteContentEntry::query()->whereIn('key', array_keys($fields))->get()->keyBy('key');

        // Upload every new photo before changing anything, so a failed upload leaves
        // the site as it was and nothing orphaned in Cloudinary.
        $uploaded = [];

        try {
            foreach (array_filter($this->photos) as $name => $file) {
                $uploaded[$name] = $cloudinary->upload($file, (string) config('site_content.folder'));
            }
        } catch (RuntimeException $e) {
            report($e);

            foreach ($uploaded as $done) {
                $cloudinary->destroy($done['public_id']);
            }

            $this->addError('photos.'.array_key_first(array_diff_key(array_filter($this->photos), $uploaded)), __('This photo could not be uploaded. Check the Cloudinary settings and try again.'));

            return;
        }

        $toDelete = [];

        foreach ($fields as $key => $field) {
            $name = $this->name($key);
            $entry = $existing->get($key);

            if ($field['type'] === 'image') {
                $alt = trim($this->alts[$name] ?? '');

                if (isset($uploaded[$name])) {
                    $toDelete[] = $entry?->public_id;

                    SiteContentEntry::updateOrCreate(['key' => $key], [
                        'value' => $uploaded[$name]['url'],
                        'public_id' => $uploaded[$name]['public_id'],
                        'alt' => $alt,
                    ]);
                } elseif (! empty($this->restore[$name])) {
                    $toDelete[] = $entry?->public_id;
                    $entry?->delete();
                } elseif ($entry !== null && $entry->alt !== $alt) {
                    $entry->update(['alt' => $alt]);
                }

                continue;
            }

            $value = $this->normalise($field, $this->values[$name] ?? '');

            if ($value === (string) $field['default'] || ($value === '' && empty($field['optional']))) {
                $entry?->delete();
            } else {
                SiteContentEntry::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        foreach (array_filter($toDelete) as $publicId) {
            $cloudinary->destroy($publicId);
        }

        $content->flush();
        $this->fillFromContent($content);
        $this->version++;

        session()->flash('status', __('Saved. The site shows your changes now.'));
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function normalise(array $field, string $value): string
    {
        return match ($field['type']) {
            'lines' => implode("\n", SiteContent::splitLines($value)),
            'textarea' => trim(preg_replace("/[ \t]*\R[ \t]*/", ' ', $value) ?? $value),
            default => trim($value),
        };
    }

    /**
     * Whether the form differs from what the site shows now.
     */
    protected function hasChanges(SiteContent $content): bool
    {
        if (array_filter($this->photos) !== [] || $this->restore !== []) {
            return true;
        }

        foreach ($content->fields($this->section) as $key => $field) {
            $name = $this->name($key);

            if ($field['type'] === 'image') {
                $changed = $content->image($key) !== null && trim($this->alts[$name] ?? '') !== $content->alt($key);
            } else {
                // What the site would show after saving: an empty required field falls back.
                $value = $this->normalise($field, $this->values[$name] ?? '');
                $changed = ($value === '' && empty($field['optional']) ? (string) $field['default'] : $value) !== $content->text($key);
            }

            if ($changed) {
                return true;
            }
        }

        return false;
    }

    protected function name(string $key): string
    {
        return substr($key, strlen($this->section) + 1);
    }

    protected function key(string $name): string
    {
        return "{$this->section}.{$name}";
    }

    public function render(SiteContent $content): View
    {
        $section = $content->sections()[$this->section];

        return view('livewire.admin.site.edit', [
            'info' => $section,
            'content' => $content,
            'hasChanges' => $this->hasChanges($content),
            'previewUrl' => $section['anchor'] ? route('home').'#'.$section['anchor'] : null,
        ])->title($section['label'].' · '.__('Site content'));
    }
}

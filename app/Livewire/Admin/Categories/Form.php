<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Services\Cloudinary;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

class Form extends Component
{
    use WithFileUploads;

    public ?Category $category = null;

    public string $name = '';

    public string $description = '';

    /** @var UploadedFile|null */
    public $photo = null;

    public bool $removePhoto = false;

    public function mount(?Category $category = null): void
    {
        if ($category?->exists) {
            $this->category = $category;
            $this->name = $category->name;
            $this->description = (string) $category->description;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            // Quotes would break the shop's category filter, which embeds the name in script.
            'name' => ['required', 'string', 'max:255', 'not_regex:/[\'"\\\\]/', Rule::unique('categories', 'name')->ignore($this->category)],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function save(Cloudinary $cloudinary): void
    {
        $validated = $this->validate();

        $category = $this->category ?? new Category(['sort_order' => (int) Category::max('sort_order') + 1]);
        $oldPublicId = $category->image_public_id;
        $replacing = $this->photo !== null || $this->removePhoto;

        if ($this->photo !== null) {
            try {
                $image = $cloudinary->upload($this->photo);
            } catch (RuntimeException $e) {
                report($e);
                $this->addError('photo', __('The image could not be uploaded. Check the Cloudinary settings and try again.'));

                return;
            }

            $category->image_url = $image['url'];
            $category->image_public_id = $image['public_id'];
        } elseif ($this->removePhoto) {
            $category->image_url = null;
            $category->image_public_id = null;
            $category->image_path = null;
        }

        $category->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?: null,
        ])->save();

        if ($replacing && $oldPublicId !== null) {
            $cloudinary->destroy($oldPublicId);
        }

        session()->flash('status', __('Category saved.'));

        $this->redirectRoute('admin.categories.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.categories.form')
            ->title($this->category ? __('Edit category') : __('New category'));
    }
}

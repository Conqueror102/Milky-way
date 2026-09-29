{{-- The store's logo mark. Sized by the class passed in; the artwork keeps its shape inside it. --}}
<picture>
    <source type="image/webp" srcset="/images/logo.webp" />
    <img src="/images/logo.png" alt="" width="242" height="171" {{ $attributes->class('object-contain') }} />
</picture>

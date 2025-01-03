@foreach (['error' => 'danger', 'success' => 'success'] as $key => $type)
    @if (session($key))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="alert alert-{{ $type }} text-center fw-bold mt-4 mb-4 max-w-screen-md mx-auto">
            {{ session($key) }}
        </div>
    @endif
@endforeach

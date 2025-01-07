<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-light shadow-sm p-3 rounded">
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($loop->last)
                    <li class="breadcrumb-item active text-primary" aria-current="page">
                        <i class="bi bi-circle-fill"></i> {{ $breadcrumb['name'] }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none text-dark">
                            <i class="bi bi-arrow-right-short"></i> {{ $breadcrumb['name'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
</div>

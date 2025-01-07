@props(['cardTitle' => '', 'cardTools' => '', 'cardFooter' => ''])

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">{{ $cardTitle }}</h3>
        <div class="card-tools">
            {{ $cardTools }}
        </div>
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
    @if($cardFooter)
    <div class="card-footer bg-light text-muted">
        <div class="float-right">
            {{ $cardFooter }}
        </div>
    </div>
    @endif
</div>

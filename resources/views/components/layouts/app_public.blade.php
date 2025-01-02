<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name')}}</title>
    @include('components.layouts.partials-public.styles')
    @livewireStyles
</head>

<body class="d-flex flex-column min-vh-100">

    @include('components.layouts.partials-public.header')

    <main class="container flex-grow-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    @include('components.layouts.partials-public.footer')

    @include('components.layouts.partials-public.scripts')
    @livewireScripts
</body>

</html>

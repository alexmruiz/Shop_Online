<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @include('components.layouts.partials-public.styles')
    @livewireStyles
</head>

<body class="d-flex flex-column min-vh-100 bg-light text-dark">

    <!-- Header -->
    @include('components.layouts.partials-public.header')

    <!-- Main content -->
    <main class="container my-4 flex-grow-1">
        <!-- Breadcrumb -->
        <livewire:breadcrumb />
        <x-flash-message />
        <section class="content">
            {{ $slot }}
        </section>
    </main>

    <!-- Footer -->
    @include('components.layouts.partials-public.footer')

    @include('components.layouts.partials-public.scripts')
    @livewireScripts
</body>

</html>

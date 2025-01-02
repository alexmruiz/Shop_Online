<nav class="-mx-3 flex flex-1 justify-end">
    @guest
    <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Iniciar sesión</a>
    @if (Route::has('register'))
        <a href="{{ route('register') }}" class="btn btn-outline-success">Registrarse</a>
    @endif
@endguest
</nav>

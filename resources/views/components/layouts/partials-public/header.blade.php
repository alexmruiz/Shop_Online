<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <i class="bi bi-shop me-2 fs-4"></i>
                <span class="fw-bold">Tienda AmR</span>
            </a>

            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left Navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active text-capitalize" aria-current="page" href="#">Inicio</a>
                    </li>
                </ul>

                <!-- Right Content -->
                <div class="d-flex align-items-center">

                    <!-- Authentication Links -->
                    @if (Route::has('login'))
                    <livewire:welcome.navigation />
                    @endif

                    @auth
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="text-white dropdown-toggle text-decoration-none d-flex align-items-center"
                            id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Opciones de usuario">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <span class="fw-bold">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="#">Perfil</a></li>
                            <li><a class="dropdown-item" href="{{ route('myOrders') }}">Mis Pedidos</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

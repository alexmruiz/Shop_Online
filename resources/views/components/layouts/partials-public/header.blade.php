<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-lg">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <i class="bi bi-shop me-2 fs-3"></i>
                <span class="fw-bold fs-4">{{ __('header.shop') }} AmR</span>
            </a>

            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" href="#about">{{ __('header.about') }}</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <!-- Selector de idioma -->
                    <div class="dropdown">
                        <a class="btn btn-light dropdown-toggle" href="#" role="button" id="langMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            🌐 {{ __('header.language') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="langMenu">
                            <li>
                                <a class="dropdown-item" href="{{ route('setLang', ['locale'=> 'es']) }}">🇪🇸 {{ __('header.spanish') }}</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('setLang', ['locale'=> 'en']) }}">🇬🇧 {{ __('header.english') }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Authentication Links -->
                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif

                    @auth
                        <div class="dropdown me-2">
                            <a href="#" class="text-white dropdown-toggle text-decoration-none d-flex align-items-center"
                                id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2 fs-5"></i>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                                <li><a class="dropdown-item" href="{{ route('profile') }}">{{ __('header.profile') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('myOrders') }}">{{ __('header.my_orders') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">{{ __('header.logout') }}</button>
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

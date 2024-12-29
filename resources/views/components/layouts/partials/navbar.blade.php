  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    @auth
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Home</a>
      </li>
      @endauth

    </ul>
    @if (Route::has('login'))
      <livewire:welcome.navigation />
    @endif
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      @auth
      <li class="nav-item">
          <form action="simple-results.html">
            <div class="input-group">
                <input type="search" class="form-control" placeholder="Buscar Producto...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
      </li>
      
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <img src="{{ asset('assets/dist/img/avatar5.png') }}" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
          <!-- User image -->
          <li class="user-header bg-lightblue">
            <img src="{{ asset('assets/dist/img/avatar5.png') }}" class="img-circle elevation-2" alt="User Image">
  
            <p>
              {{ Auth::user()->name }}
            </p>
          </li>
          <!-- Menu Body -->
  
          <!-- Menu Footer-->
          <li class="user-footer">
            <a class="btn btn-default btn-flat">Perfil</a>
           <a class="btn btn-default btn-flat float-right" href=""
            onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
             Salir
         </a>
  
         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
             @csrf
         </form>
          </li>
        </ul>
      </li>
      @endauth
     
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

    </ul>
  </nav>
  <!-- /.navbar -->
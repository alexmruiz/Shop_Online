  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    
    <a href="index3.html" class="brand-link">
      <img src=" {{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Tienda AmR</span>
    </a>
   

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      @auth
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src=" {{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>       
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div>       
      </div>
      @endauth

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          @auth
               <li class="nav-item">
                <a href="{{route('dashboard')}}" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>Inicio</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('category')}}" class="nav-link">
                    <i class="nav-icon fas fa-th-list"></i>
                    <p>Categorías</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('product')}}" class="nav-link">
                    <i class="nav-icon fas fa-box"></i>
                    <p>Productos</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="
                {{ route ('client') }}" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Clientes</p>
                </a>
            </li>
            @endauth
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
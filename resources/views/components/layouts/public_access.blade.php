<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página pública</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
      body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }
      main {
        flex: 1;
      }
      footer {
        bottom: 0;
        width: 100%;
      }
    </style>
    @livewireStyles
  </head>
  <body>
    <header>
      <!-- Navbar Mejorado -->
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="bi bi-shop me-2"></i> Tienda
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Inicio</a>
              </li>             
            </ul>          
            @if (Route::has('login'))
            <livewire:welcome.navigation />
            @endif
          </div>
        </div>
      </nav>
    </header>

    <main class="container">
      <livewire:product.public-products />
    </main>

    <!-- Footer Mejorado -->
    <footer class="bg-dark text-white py-4">
      <div class="container">
        <div class="row">
          <!-- Enlaces rápidos -->
          <div class="col-md-4">
            <h5>Enlaces rápidos</h5>
            <ul class="list-unstyled">
              <li><a href="#" class="text-white text-decoration-none">Inicio</a></li>
              <li><a href="#" class="text-white text-decoration-none">Productos</a></li>
              <li><a href="#" class="text-white text-decoration-none">Contacto</a></li>
            </ul>
          </div>
          <!-- Información de contacto -->
          <div class="col-md-4">
            <h5>Contacto</h5>
            <p class="mb-1">Teléfono: +34 123 456 789</p>
            <p>Email: info@tienda.com</p>
            <p>Dirección: Calle Falsa 123, Madrid</p>
          </div>
          <!-- Redes sociales -->
          <div class="col-md-4">
            <h5>Síguenos</h5>
            <a href="#" class="text-white text-decoration-none me-3"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-white text-decoration-none me-3"><i class="bi bi-twitter"></i></a>
            <a href="#" class="text-white text-decoration-none me-3"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
        <div class="text-center mt-3">
          <p>&copy; 2024 Tienda. Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
    @livewireScripts
  </body>
</html>

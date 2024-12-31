<div>
    <div class="container">
        <!-- Mensaje de Bienvenida -->
        <h1>¡Hola, {{ auth()->user()->name }}!</h1>
        <p>Nos alegra verte nuevamente. ¿Qué quieres hacer hoy?</p>
        
        <!-- Acciones rápidas -->
        <div class="quick-actions my-4">          
          <a href="{{ route('product') }}" class="btn btn-primary"><i class="fas fa-box"></i> Ir a productos</a>
          <a href="{{ route('client') }}" class="btn btn-secondary"><i class="fas fa-user"></i> Ver Clientes</a>
          <a href="#" class="btn btn-warning"><i class="fas fa-chart-line"></i> Generar Reportes</a>
        </div>
      
        <!-- Panel de Resumen -->
        <div class="row">
          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="fas fa-box"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Productos</span>
                <span class="info-box-number">{{$this->totalRegistrosProduct}}</span>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Clientes</span>
                <span class="info-box-number">{{$this->totalRegistrosClient}}</span>
              </div>
            </div>
          </div>
        </div>
      
        <!-- Notificaciones -->
        <div class="alert alert-info mt-4">
          <i class="fas fa-info-circle"></i> ¡Recuerda actualizar tus datos para mejorar tu experiencia!
        </div>
      </div>
      
    
</div>




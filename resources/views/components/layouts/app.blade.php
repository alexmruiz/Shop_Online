<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name')}}</title>

  @include('components.layouts.partials.styles')

</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src=" {{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  @include('components.layouts.partials.navbar')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  @include('components.layouts.partials.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @include('components.layouts.partials.content-header')
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @livewire('message')
        {{ $slot }}      
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



  <!-- Main Footer -->
  @include('components.layouts.partials.footer')
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
@include('components.layouts.partials.scripts')

<!-- PLUGINS -->
<script>
  document.addEventListener('livewire:init', () => {
      Livewire.on('close-modal', (idModal) => {
          $('#' + idModal).modal('hide');
      });
  });

  document.addEventListener('livewire:init', () => {
      Livewire.on('open-modal', (idModal) => {
          $('#' + idModal).modal('show');
      });
  });

  document.addEventListener('livewire:init', () => {
      Livewire.on('delete', (e) => {
        //alert(e.id+ '-' + e.eventName)
          Swal.fire({
              title: "Estas seguro?",
              text: "Esta acción no se puede revertir!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Si, eliminar!",
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (result.isConfirmed) {
                Livewire.dispatch(e.eventName, { id: e.id });

              }
          });

      });
  });
</script>
</body>
</html>

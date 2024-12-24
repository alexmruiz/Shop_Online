<x-card cardTitle="Detalles del producto">
    <x-slot:cardTools>
        <a href="{{ route('product') }}" class="btn btn-primary">
            <i class="fas fa-arrow-circle-left"></i> Regresar
        </a>
    </x-slot>
    <!-- Default box -->
    <div class="card card-solid">
        <div class="card-body">
            <div class="row">
                
                <div class="col-12 col-sm-7">
                    <h3 class="my-3">{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>

                    <hr>

                    <div class="row">
                       


                        <!-- Caja categoria -->
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-th-large"></i>
                                </span>
                                <div class="info-box-content">

                                    <span class="info-box-text">Categoria</span>
                                    <span class="info-box-number">{{ $product->category->name }}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>

                        <!-- Caja estado -->
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-toggle-on"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Estado</span>
                                    <span class="info-box-number">{!! $product->activeLabel !!}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>


                        <!-- Caja fecha creacion -->
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-calendar-plus"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fecha creacion</span>
                                    <span class="info-box-number">{{ $product->created_at }}</span>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="bg-lightblue py-2 px-3 mt-4 col-md-6">
                            <h2 class="mb-0">
                                {!! $product->price !!}€
                            </h2>
                            <h4 class="mt-0">
                                <small>Precio </small>
                            </h4>
                        </div>
                        
                    </div>

                </div>
            </div>

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->




</x-card>

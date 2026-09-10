<div>
    <div class="container py-5">
        <!-- Mensaje de Bienvenida -->
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">¡Hola, {{ auth()->user()->name }}!</h1>
            <p class="lead text-muted">Nos alegra verte nuevamente. ¿Qué quieres hacer hoy?</p>
        </div>

        <!-- Acciones rápidas -->
        <div class="d-flex flex-wrap justify-content-center gap-3 my-4 mb-4">
            <a href="{{ route('product') }}" class="btn btn-primary btn-lg d-flex align-items-center gap-2">
                <i class="fas fa-box"></i> Ir a productos
            </a>
            <a href="{{ route('client') }}" class="btn btn-secondary btn-lg d-flex align-items-center gap-2">
                <i class="fas fa-user"></i> Ver Clientes
            </a>
            <a href="#" class="btn btn-warning btn-lg d-flex align-items-center gap-2">
                <i class="fas fa-chart-line"></i> Generar Reportes
            </a>
        </div>

        <!-- Panel de Resumen -->
        <div class="row justify-content-center g-4 mt-4">
            <!-- Productos -->
            <div class="col-md-3">
                <div class="card text-white bg-info shadow text-center">
                    <div class="card-body">
                        <i class="fas fa-box fa-3x mb-3"></i>
                        <h5 class="card-title">Productos</h5>
                        <p class="card-text fs-4">{{ $this->totalRegistrosProduct }}</p>
                    </div>
                </div>
            </div>

            <!-- Clientes -->
            <div class="col-md-3">
                <div class="card text-white bg-success shadow text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text fs-4">{{ $this->totalRegistrosClient }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h2 class="text-center">Productos Más Vendidos</h2>
            <div style="max-width: 600px; height: 400px; margin: 0 auto;">
                <canvas id="topProductsChart"></canvas>
            </div>

        </div>

        <!-- Notificaciones -->
        <div class="alert alert-info mt-5 text-center shadow-sm">
            <i class="fas fa-info-circle"></i> ¡Recuerda actualizar tus datos para mejorar tu experiencia!
        </div>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('topProductsChart').getContext('2d');

        // Datos proporcionados por el backend
        const topSellingProducts = @json($topSellingProducts);

        if (topSellingProducts.length > 0) {
            // Extraer etiquetas (nombres) y datos (cantidad vendida)
            const labels = topSellingProducts.map(product => product.name);
            const data = topSellingProducts.map(product => Number(product
                .total_sold)); // Asegurar que sea numérico

            // Crear el gráfico
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: data,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)', // Azul
                            'rgba(255, 99, 132, 0.7)', // Rojo
                            'rgba(255, 206, 86, 0.7)', // Amarillo
                            'rgba(75, 192, 192, 0.7)', // Verde
                            'rgba(153, 102, 255, 0.7)' // Morado
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 2, // Bordes más gruesos
                        borderRadius: 5, // Bordes redondeados
                        barPercentage: 0.8, // Más delgado
                        categoryPercentage: 0.6, // Espacio entre barras
                        hoverBackgroundColor: 'rgba(0, 0, 0, 0.1)', // Efecto hover
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Permite ajustar el tamaño
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: '#333'
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 12
                                },
                                color: '#555'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1,
                                font: {
                                    size: 12
                                },
                                color: '#555'
                            },
                            grid: {
                                borderDash: [5, 5], // Líneas punteadas
                                color: 'rgba(200, 200, 200, 0.3)'
                            }
                        }
                    }
                }
            });
        } else {
            console.error("No hay datos disponibles para el gráfico.");
        }
    });
</script>

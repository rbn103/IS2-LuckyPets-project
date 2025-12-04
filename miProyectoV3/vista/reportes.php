<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Simular datos de compras basados en los pedidos
$compras_totales = 85640;
$productos_comprados = 1248;
$costo_promedio = 68.50;
$margen_ganancia = 42.5;

// Datos para gráficos
$compras_por_categoria = [
    'Alimento Seco' => 45,
    'Alimento Húmedo' => 25,
    'Snacks' => 15,
    'Fórmula' => 12,
    'Accesorios' => 3
];

$tendencia_mensual = [42000, 45000, 48000, 52000, 55000, 53000, 56000, 58000, 62000, 60000, 65000, 68000];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>LuckyPets Inventory - Reportes de Compras</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#795548",
                        "background-light": "#F7F8FC",
                        "background-dark": "#121212",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E1E1E",
                        "sidebar-light": "#FDFBF8",
                        "sidebar-dark": "#242424",
                        "border-light": "#E0E0E0",
                        "border-dark": "#333333",
                        "text-light": "#111827",
                        "text-dark": "#E5E7EB",
                        "text-secondary-light": "#6B7280",
                        "text-secondary-dark": "#9CA3AF",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
          font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24
        }
        .user-info {
            background: #4e2c06;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark">
<div class="flex h-screen">
<aside class="w-64 flex-shrink-0 bg-sidebar-light dark:bg-sidebar-dark p-6 flex flex-col justify-between">
<div>
<div class="flex justify-between items-center mb-10">
<div>
<h1 class="text-xl font-bold text-text-light dark:text-text-dark">LuckyPets</h1>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Inventario</p>
</div>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark">
<span class="material-symbols-outlined">close</span>
</button>
</div>

<!-- Información del usuario -->
<div class="user-info">
    <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['user']['username']); ?><br>
    <small>Conectado</small>
</div>

<nav class="space-y-2">
<a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="productos.php">
<span class="material-symbols-outlined">inventory_2</span>
<span class="font-medium">Productos</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="control_pedidos.php">
<span class="material-symbols-outlined">local_shipping</span>
<span class="font-medium">Control de Pedidos</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="proveedores.php">
<span class="material-symbols-outlined">groups</span>
<span class="font-medium">Proveedores</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg shadow-md" href="reportes.php">
<span class="material-symbols-outlined">trending_up</span>
<span class="font-semibold">Reportes</span>
</a>
</nav>
</div>
<div class="space-y-2 border-t border-border-light dark:border-border-dark pt-4">
<a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="configuracion.php">
<span class="material-symbols-outlined">settings</span>
<span class="font-medium">Configuración</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" href="../controlador/logout.php">
<span class="material-symbols-outlined">logout</span>
<span class="font-medium">Cerrar Sesión</span>
</a>
</div>
</aside>

<main class="flex-1 p-8 overflow-y-auto">
<!-- Filtros de Reportes -->


<!-- Métricas Principales de COMPRAS -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
        <div>
            <p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Compras Totales</p>
            <p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">S/. <?php echo number_format($compras_totales, 0); ?></p>
            <p class="text-xs text-green-500 mt-1">↑ 15.2% vs mes anterior</p>
        </div>
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
            <span class="material-symbols-outlined text-blue-500">shopping_cart</span>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
        <div>
            <p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Productos Comprados</p>
            <p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark"><?php echo number_format($productos_comprados, 0); ?></p>
            <p class="text-xs text-green-500 mt-1">↑ 8.7% vs mes anterior</p>
        </div>
        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-full">
            <span class="material-symbols-outlined text-green-500">inventory_2</span>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
        <div>
            <p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Costo Promedio Unitario</p>
            <p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">S/. <?php echo number_format($costo_promedio, 2); ?></p>
            <p class="text-xs text-red-500 mt-1">↑ 3.2% vs mes anterior</p>
        </div>
        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-full">
            <span class="material-symbols-outlined text-purple-500">receipt</span>
        </div>
    </div>
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
        <div>
            <p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Margen Estimado</p>
            <p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark"><?php echo $margen_ganancia; ?>%</p>
            <p class="text-xs text-red-500 mt-1">↓ 1.8% vs mes anterior</p>
        </div>
        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
            <span class="material-symbols-outlined text-yellow-500">trending_up</span>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Gráfico de Compras por Categoría -->
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-text-light dark:text-text-dark">Compras por Categoría de Producto</h3>
            <button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark">
                <span class="material-symbols-outlined">more_vert</span>
            </button>
        </div>
        <div class="chart-container">
            <canvas id="categoriaChart"></canvas>
        </div>
    </div>

    <!-- Gráfico de Tendencia de Compras -->
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-text-light dark:text-text-dark">Tendencia de Compras Mensual</h3>
            <button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark">
                <span class="material-symbols-outlined">more_vert</span>
            </button>
        </div>
        <div class="chart-container">
            <canvas id="tendenciaChart"></canvas>
        </div>
    </div>
</div>

<!-- Tablas de Reportes de COMPRAS -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top Productos Más Comprados -->
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-text-light dark:text-text-dark">Top 5 Productos Más Comprados</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-text-secondary-light dark:text-text-secondary-dark uppercase">
                    <tr>
                        <th class="py-3 px-4 font-medium">Producto</th>
                        <th class="py-3 px-4 font-medium text-right">Unidades Compradas</th>
                        <th class="py-3 px-4 font-medium text-right">Inversión Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Alimento Seco Adulto</td>
                        <td class="py-3 px-4 text-right">320</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 14,400</td>
                    </tr>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Snacks para Cachorro</td>
                        <td class="py-3 px-4 text-right">285</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 5,415</td>
                    </tr>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Alimento Húmedo</td>
                        <td class="py-3 px-4 text-right">198</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 11,880</td>
                    </tr>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Fórmula Especial</td>
                        <td class="py-3 px-4 text-right">156</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 17,160</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Alimento Seco Mayor</td>
                        <td class="py-3 px-4 text-right">142</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 5,964</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Compras por Proveedor -->
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-text-light dark:text-text-dark">Compras por Proveedor</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-text-secondary-light dark:text-text-secondary-dark uppercase">
                    <tr>
                        <th class="py-3 px-4 font-medium">Proveedor</th>
                        <th class="py-3 px-4 font-medium text-right">Total Compras</th>
                        <th class="py-3 px-4 font-medium text-right">Pedidos</th>
                        <th class="py-3 px-4 font-medium text-center">Confiabilidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">RINTI S.A.</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 28,450</td>
                        <td class="py-3 px-4 text-right">24</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Alta
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">SuperPet</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 22,180</td>
                        <td class="py-3 px-4 text-right">18</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Alta
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-border-light dark:border-border-dark">
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Pets Place</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 18,760</td>
                        <td class="py-3 px-4 text-right">15</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Media
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-medium text-text-light dark:text-text-dark">Fami Pet</td>
                        <td class="py-3 px-4 text-right text-blue-600">S/. 15,250</td>
                        <td class="py-3 px-4 text-right">12</td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Media
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
</div>

<script>
// Gráfico de Compras por Categoría
const categoriaCtx = document.getElementById('categoriaChart').getContext('2d');
const categoriaChart = new Chart(categoriaCtx, {
    type: 'doughnut',
    data: {
        labels: ['Alimento Seco', 'Alimento Húmedo', 'Snacks', 'Fórmula', 'Accesorios'],
        datasets: [{
            data: [45, 25, 15, 12, 3],
            backgroundColor: [
                '#4f46e5',
                '#10b981',
                '#f59e0b',
                '#8b5cf6',
                '#6b7280'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: document.body.classList.contains('dark') ? '#E5E7EB' : '#111827'
                }
            },
            title: {
                display: true,
                text: 'Distribución de Compras por Categoría',
                color: document.body.classList.contains('dark') ? '#E5E7EB' : '#111827'
            }
        }
    }
});

// Gráfico de Tendencia de Compras
const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
const tendenciaChart = new Chart(tendenciaCtx, {
    type: 'line',
    data: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
            label: 'Compras (S/.)',
            data: [42000, 45000, 48000, 52000, 55000, 53000, 56000, 58000, 62000, 60000, 65000, 68000],
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: false,
                grid: {
                    color: document.body.classList.contains('dark') ? '#333333' : '#E0E0E0'
                },
                ticks: {
                    color: document.body.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                }
            },
            x: {
                grid: {
                    color: document.body.classList.contains('dark') ? '#333333' : '#E0E0E0'
                },
                ticks: {
                    color: document.body.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: document.body.classList.contains('dark') ? '#E5E7EB' : '#111827'
                }
            }
        }
    }
});

// Actualizar colores de gráficos cuando cambie el modo oscuro
const observer = new MutationObserver(() => {
    categoriaChart.update();
    tendenciaChart.update();
});
observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
</script>
</body>
</html>
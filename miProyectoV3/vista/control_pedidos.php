<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>LuckyPets Inventory - Control de Pedidos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
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
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg shadow-md" href="control_pedidos.php">
<span class="material-symbols-outlined">local_shipping</span>
<span class="font-semibold">Control de Pedidos</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="proveedores.php">
<span class="material-symbols-outlined">groups</span>
<span class="font-medium">Proveedores</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="reportes.php">
<span class="material-symbols-outlined">trending_up</span>
<span class="font-medium">Reportes</span>
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

<!-- El contenido principal de control de pedidos permanece igual -->
<main class="flex-1 p-8 overflow-y-auto">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Total Pedidos</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">9</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">Registrados</p>
</div>
<div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-full">
<span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">list_alt</span>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Pedidos Programados</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">4</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">A futuro</p>
</div>
<div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
<span class="material-symbols-outlined text-blue-500">event</span>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Pedidos Abiertos</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">3</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">En proceso</p>
</div>
<div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
<span class="material-symbols-outlined text-yellow-500">hourglass_top</span>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Pedidos Directos</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">2</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">Compra inmediata</p>
</div>
<div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-full">
<span class="material-symbols-outlined text-green-500">bolt</span>
</div>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
<h2 class="text-lg font-semibold mb-4 text-text-light dark:text-text-dark">Lista de Pedidos</h2>
<div class="overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="text-xs text-text-secondary-light dark:text-text-secondary-dark uppercase">
<tr>
<th class="py-3 px-4 font-medium" scope="col">Pedido</th>
<th class="py-3 px-4 font-medium" scope="col">Código Producto</th>
<th class="py-3 px-4 font-medium" scope="col">Categoría</th>
<th class="py-3 px-4 font-medium" scope="col">Fecha del Pedido</th>
<th class="py-3 px-4 font-medium" scope="col">Fecha de Llegada</th>
<th class="py-3 px-4 font-medium" scope="col">Cantidad</th>
<th class="py-3 px-4 font-medium" scope="col">Precio de Compra Unitario</th>
<th class="py-3 px-4 font-medium text-center" scope="col">Acciones</th>
</tr>
</thead>
<tbody>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED001</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD004</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Programado</span></td>
<td class="py-4 px-4">12/12/2025</td>
<td class="py-4 px-4">14/12/2025</td>
<td class="py-4 px-4">10</td>
<td class="py-4 px-4">S/.12.50</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED002</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD006</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Programado</span></td>
<td class="py-4 px-4">10/12/2025</td>
<td class="py-4 px-4">12/12/2025</td>
<td class="py-4 px-4">20</td>
<td class="py-4 px-4">S/.28.00</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED003</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD001</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Abierto</span></td>
<td class="py-4 px-4">10/12/2025</td>
<td class="py-4 px-4">14/11/2025</td>
<td class="py-4 px-4">35</td>
<td class="py-4 px-4">S/.32.00</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED004</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD002</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Directo</span></td>
<td class="py-4 px-4">02/12/2025</td>
<td class="py-4 px-4">05/12/2025</td>
<td class="py-4 px-4">48</td>
<td class="py-4 px-4">S/.18.50</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED005</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD009</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Programado</span></td>
<td class="py-4 px-4">19/11/2025</td>
<td class="py-4 px-4">22/11/2025</td>
<td class="py-4 px-4">56</td>
<td class="py-4 px-4">S/.45.00</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED006</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD007</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Abierto</span></td>
<td class="py-4 px-4">17/11/2025</td>
<td class="py-4 px-4">19/11/2025</td>
<td class="py-4 px-4">60</td>
<td class="py-4 px-4">S/.8.50</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED007</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD005</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Directo</span></td>
<td class="py-4 px-4">28/11/2025</td>
<td class="py-4 px-4">01/12/2025</td>
<td class="py-4 px-4">46</td>
<td class="py-4 px-4">S/.52.00</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED008</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD008</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Programado</span></td>
<td class="py-4 px-4">20/11/2025</td>
<td class="py-4 px-4">22/11/2025</td>
<td class="py-4 px-4">30</td>
<td class="py-4 px-4">S/.68.50</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
<tr>
<td class="py-4 px-4 font-medium text-text-light dark:text-text-dark">PED009</td>
<td class="py-4 px-4 text-text-secondary-light dark:text-text-secondary-dark">PROD003</td>
<td class="py-4 px-4"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Abierto</span></td>
<td class="py-4 px-4">15/12/2025</td>
<td class="py-4 px-4">17/12/2025</td>
<td class="py-4 px-4">34</td>
<td class="py-4 px-4">S/.42.30</td>
<td class="py-4 px-4 text-center">
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-blue-500 dark:hover:text-blue-400 mr-2"><span class="material-symbols-outlined text-base">visibility</span></button>
<button class="text-text-secondary-light dark:text-text-secondary-dark hover:text-green-500 dark:hover:text-green-400"><span class="material-symbols-outlined text-base">update</span></button>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</main>
</div>
</body>
</html>
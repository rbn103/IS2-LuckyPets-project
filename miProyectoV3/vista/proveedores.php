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
    <title>LuckyPets Inventory - Proveedores</title>
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
<a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg shadow-md" href="proveedores.php">
<span class="material-symbols-outlined">groups</span>
<span class="font-semibold">Proveedores</span>
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

<main class="flex-1 p-8 overflow-y-auto">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Total Proveedores</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">9</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">Proveedores activos</p>
</div>
<div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-full">
<span class="material-symbols-outlined text-text-secondary-light dark:text-text-secondary-dark">groups</span>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Confiabilidad Alta</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">5</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">4-5 estrellas</p>
</div>
<div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
<span class="material-symbols-outlined text-yellow-500" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark flex justify-between items-start">
<div>
<p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Confiabilidad Media</p>
<p class="text-2xl font-bold mt-1 text-text-light dark:text-text-dark">3</p>
<p class="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">3 estrellas</p>
</div>
<div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-full">
<span class="material-symbols-outlined text-yellow-500" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</div>
</div>
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
<div class="flex justify-between items-center mb-4">
<h2 class="text-lg font-semibold text-text-light dark:text-text-dark">Lista de Proveedores</h2>
</div>
<div class="overflow-x-auto">
<table class="w-full text-sm text-left">
<thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-800">
<tr>
<th class="py-3 px-4 font-medium" scope="col">RUC</th>
<th class="py-3 px-4 font-medium" scope="col">Marca</th>
<th class="py-3 px-4 font-medium" scope="col">Razón Social</th>
<th class="py-3 px-4 font-medium" scope="col">Confiabilidad</th>
<th class="py-3 px-4 font-medium" scope="col">Sitio Web</th>
<th class="py-3 px-4 font-medium" scope="col">Acciones</th>
</tr>
</thead>
<tbody>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20548058610</td>
<td class="py-4 px-4">Canbo</td>
<td class="py-4 px-4">RINTI S.A.</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20603306638</td>
<td class="py-4 px-4">Barker</td>
<td class="py-4 px-4">Pets Place</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20609101416</td>
<td class="py-4 px-4">Mimaskot</td>
<td class="py-4 px-4">Fami Pet</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20537150522</td>
<td class="py-4 px-4">Pedigree</td>
<td class="py-4 px-4">SuperPet</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20100617332</td>
<td class="py-4 px-4">Ricocan</td>
<td class="py-4 px-4">RINTI S.A.</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20263322496</td>
<td class="py-4 px-4">Purina Pro Plan</td>
<td class="py-4 px-4">SuperPet</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20100096341</td>
<td class="py-4 px-4">Bayer</td>
<td class="py-4 px-4">Pets Place</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
</div>
</td>
<td class="py-4 px-4 text-gray-500">No disponible</td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr class="border-b border-border-light dark:border-border-dark">
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20601333351</td>
<td class="py-4 px-4">AllKJoy S.A.C.</td>
<td class="py-4 px-4">Pets Place La Molina</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
</td>
</tr>
<tr>
<td class="py-4 px-4 font-medium text-gray-900 dark:text-white">20548058610</td>
<td class="py-4 px-4">Pet Nutriscience</td>
<td class="py-4 px-4">Veterinaria Animal Health La Molina</td>
<td class="py-4 px-4">
<div class="flex items-center text-yellow-400">
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-sm text-gray-300">star</span>
</div>
</td>
<td class="py-4 px-4"><a class="flex items-center text-blue-600 hover:underline" href="#"><span class="material-symbols-outlined mr-1 text-base">public</span>Visitar</a></td>
<td class="py-4 px-4">
<div class="flex items-center space-x-3">
<button class="text-gray-500 hover:text-gray-700"><span class="material-symbols-outlined text-lg">edit</span></button>
<button class="text-red-500 hover:text-red-700"><span class="material-symbols-outlined text-lg">delete</span></button>
</div>
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
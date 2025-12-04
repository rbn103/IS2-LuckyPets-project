<?php
session_start();

// Verificar que el usuario esté autenticado y sea gerente (rol_ID = 1)
if (!isset($_SESSION['user']) || $_SESSION['user']['id_trabajador'] != 1) {
    header('Location: login.php');
    exit;
}
// Verificar rol de gerente (rol_ID = 1)
if ($_SESSION['user']['rol_id'] != 1) {
    header('Location: productos.php');
    exit;
}

// Incluir configuración de base de datos desde modelo/
require_once '../modelo/config.php';

// Variables para mensajes
$mensaje = '';
$mensaje_tipo = ''; // success, error, warning
$usuario_editando = null;

// Función para obtener todos los usuarios
function obtenerUsuarios($busqueda = '') {
    $conn = conectarDB();
    $sql = "SELECT u.*, r.nombre_rol 
            FROM Usuarios u 
            LEFT JOIN Roles r ON u.rol_ID = r.rol_ID 
            WHERE u.estado = 'activo'";
    
    if (!empty($busqueda)) {
        $sql .= " AND (u.nombre_usuario LIKE ? OR u.EMAIL LIKE ? OR u.RUC LIKE ?)";
    }
    
    $sql .= " ORDER BY u.id_trabajador ASC";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($busqueda)) {
        $busqueda_like = "%{$busqueda}%";
        $stmt->bind_param("sss", $busqueda_like, $busqueda_like, $busqueda_like);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $usuarios = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $usuarios;
}

// Función para obtener roles
function obtenerRoles() {
    $conn = conectarDB();
    $sql = "SELECT * FROM Roles ORDER BY rol_ID";
    $result = $conn->query($sql);
    $roles = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $roles;
}

// Función para obtener usuario por ID
function obtenerUsuarioPorID($id) {
    $conn = conectarDB();
    $sql = "SELECT * FROM Usuarios WHERE id_trabajador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $usuario;
}

// Procesar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['busqueda']);
    $usuarios = obtenerUsuarios($busqueda);
} else {
    $usuarios = obtenerUsuarios();
    $busqueda = '';
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    
    if ($id_eliminar != 1) { // No permitir eliminar al gerente principal
        $conn = conectarDB();
        $sql = "UPDATE Usuarios SET estado = 'inactivo' WHERE id_trabajador = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_eliminar);
        
        if ($stmt->execute()) {
            $mensaje = "Usuario eliminado correctamente (marcado como inactivo)";
            $mensaje_tipo = "success";
        } else {
            $mensaje = "Error al eliminar usuario: " . $conn->error;
            $mensaje_tipo = "error";
        }
        
        $stmt->close();
        $conn->close();
        
        // Refrescar lista
        header("Location: configuracion.php?mensaje=" . urlencode($mensaje) . "&tipo=" . $mensaje_tipo);
        exit;
    }
}

// Procesar edición (mostrar formulario)
if (isset($_GET['editar'])) {
    $id_editar = intval($_GET['editar']);
    $usuario_editando = obtenerUsuarioPorID($id_editar);
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id_trabajador = intval($_POST['id_trabajador']);
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $email = trim($_POST['email']);
    $ruc = trim($_POST['ruc']);
    $rol_id = intval($_POST['rol_id']);
    
    // Validaciones básicas
    if (empty($nombre_usuario) || empty($email)) {
        $mensaje = "Nombre de usuario y email son obligatorios";
        $mensaje_tipo = "error";
    } else {
        $conn = conectarDB();
        
        // Verificar si el email ya existe en otro usuario
        $sql_verificar = "SELECT id_trabajador FROM Usuarios WHERE EMAIL = ? AND id_trabajador != ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("si", $email, $id_trabajador);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        
        if ($result_verificar->num_rows > 0) {
            $mensaje = "El email ya está registrado por otro usuario";
            $mensaje_tipo = "error";
        } else {
            // Actualizar usuario
            $sql = "UPDATE Usuarios SET 
                    nombre_usuario = ?, 
                    RUC = ?, 
                    rol_ID = ?, 
                    EMAIL = ? 
                    WHERE id_trabajador = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisi", $nombre_usuario, $ruc, $rol_id, $email, $id_trabajador);
            
            if ($stmt->execute()) {
                $mensaje = "Usuario actualizado correctamente";
                $mensaje_tipo = "success";
                $usuario_editando = null; // Limpiar formulario de edición
            } else {
                $mensaje = "Error al actualizar usuario: " . $conn->error;
                $mensaje_tipo = "error";
            }
            
            $stmt->close();
        }
        
        $stmt_verificar->close();
        $conn->close();
    }
}

// Procesar registro de nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $nombre_usuario = trim($_POST['nuevo_nombre_usuario']);
    $email = trim($_POST['nuevo_email']);
    $ruc = trim($_POST['nuevo_ruc']);
    $rol_id = intval($_POST['nuevo_rol_id']);
    $contrasena = trim($_POST['nuevo_contrasena']);
    $confirmar_contrasena = trim($_POST['confirmar_contrasena']);
    
    // Validaciones
    if (empty($nombre_usuario) || empty($email) || empty($contrasena)) {
        $mensaje = "Todos los campos son obligatorios";
        $mensaje_tipo = "error";
    } elseif ($contrasena !== $confirmar_contrasena) {
        $mensaje = "Las contraseñas no coinciden";
        $mensaje_tipo = "error";
    } else {
        $conn = conectarDB();
        
        // Verificar si el usuario o email ya existen
        $sql_verificar = "SELECT id_trabajador FROM Usuarios WHERE nombre_usuario = ? OR EMAIL = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("ss", $nombre_usuario, $email);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        
        if ($result_verificar->num_rows > 0) {
            $mensaje = "El nombre de usuario o email ya están registrados";
            $mensaje_tipo = "error";
        } else {
            // Obtener el siguiente ID disponible
            $sql_max_id = "SELECT MAX(id_trabajador) as max_id FROM Usuarios";
            $result_max = $conn->query($sql_max_id);
            $row_max = $result_max->fetch_assoc();
            $nuevo_id = $row_max['max_id'] + 1;
            
            // Insertar nuevo usuario (con contraseña en texto plano como en el sistema actual)
            $sql = "INSERT INTO Usuarios (id_trabajador, nombre_usuario, RUC, rol_ID, EMAIL, CONTRASEÑA, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, 'activo')";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ississ", $nuevo_id, $nombre_usuario, $ruc, $rol_id, $email, $contrasena);
            
            if ($stmt->execute()) {
                $mensaje = "Usuario registrado exitosamente con ID: " . $nuevo_id;
                $mensaje_tipo = "success";
                
            } else {
                $mensaje = "Error al registrar usuario: " . $conn->error;
                $mensaje_tipo = "error";
            }
            
            $stmt->close();
        }
        
        $stmt_verificar->close();
        $conn->close();
    }
}

// Obtener roles para los formularios
$roles = obtenerRoles();

// Obtener mensaje de URL si existe
if (isset($_GET['mensaje'])) {
    $mensaje = urldecode($_GET['mensaje']);
    $mensaje_tipo = $_GET['tipo'] ?? 'info';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuckyPets - Gestión de Usuarios</title>
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
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .sidebar {
            background: #FDFBF8;
        }
        .dark .sidebar {
            background: #242424;
        }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark">
<div class="flex min-h-screen">
    <!-- Sidebar (similar a otros archivos) -->
    <aside class="w-64 flex-shrink-0 sidebar dark:bg-sidebar-dark p-6 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-xl font-bold text-text-light dark:text-text-dark">LuckyPets</h1>
                    <p class="text-sm text-text-secondary-light dark:text-text-secondary-dark">Inventario</p>
                </div>
            </div>

            <div class="bg-amber-800 text-white p-3 rounded-lg mb-4 text-center">
                <strong>Gerente:</strong> <?php echo htmlspecialchars($_SESSION['user']['username']); ?><br>
                <small>Gestor de Usuarios</small>
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
                <a class="flex items-center gap-3 px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" href="reportes.php">
                    <span class="material-symbols-outlined">trending_up</span>
                    <span class="font-medium">Reportes</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-lg shadow-md" href="configuracion.php">
                    <span class="material-symbols-outlined">settings</span>
                    <span class="font-semibold">Gestión de Usuarios</span>
                </a>
            </nav>
        </div>
        <div class="space-y-2 border-t border-border-light dark:border-border-dark pt-4">
            <a class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" href="../controlador/logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-medium">Cerrar Sesión</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">

        <h1 class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark">Gestión de Usuarios</h1>

        <!-- Sección 1: Búsqueda de Usuarios -->
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark mb-6">
            <h2 class="text-lg font-semibold mb-4">Buscar Usuario</h2>
            <form method="POST" class="flex gap-4">
                <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" 
                       placeholder="Buscar por nombre, email o RUC..." 
                       class="flex-1 px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                <button type="submit" name="buscar" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-amber-800">
                    Buscar
                </button>
                <?php if (!empty($busqueda)): ?>
                    <a href="configuracion.php" class="px-6 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600">
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Sección 2: Formulario de Registro -->
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark mb-6">
            <h2 class="text-lg font-semibold mb-4">Registrar Nuevo Usuario</h2>
            <form id="formRegistro" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nombre de Usuario *</label>
                    <input type="text" name="nuevo_nombre_usuario" required 
                           class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Email *</label>
                    <input type="email" name="nuevo_email" required 
                           class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">RUC</label>
                    <input type="text" name="nuevo_ruc" 
                           class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Rol *</label>
                    <select name="nuevo_rol_id" required 
                            class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['rol_ID']; ?>">
                                <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Contraseña *</label>
                    <input type="password" name="nuevo_contrasena" required 
                           class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Confirmar Contraseña *</label>
                    <input type="password" name="confirmar_contrasena" required 
                           class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="registrar" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        <span class="material-symbols-outlined align-middle mr-2">person_add</span>
                        Registrar Usuario
                    </button>
                </div>
            </form>
        </div>

        <!-- Sección 3: Formulario de Edición (si está activo) -->
        <?php if ($usuario_editando): ?>
            <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark mb-6 border-l-4 border-l-yellow-500">
                <h2 class="text-lg font-semibold mb-4">Editar Usuario: <?php echo htmlspecialchars($usuario_editando['nombre_usuario']); ?></h2>
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="id_trabajador" value="<?php echo $usuario_editando['id_trabajador']; ?>">
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">Nombre de Usuario *</label>
                        <input type="text" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario_editando['nombre_usuario']); ?>" required 
                               class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Email *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario_editando['EMAIL']); ?>" required 
                               class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">RUC</label>
                        <input type="text" name="ruc" value="<?php echo htmlspecialchars($usuario_editando['RUC']); ?>" 
                               class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Rol *</label>
                        <select name="rol_id" required 
                                class="w-full px-4 py-2 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800">
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['rol_ID']; ?>" 
                                    <?php echo $rol['rol_ID'] == $usuario_editando['rol_ID'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rol['nombre_rol']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-4">
                        <button type="submit" name="actualizar" 
                                class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                            <span class="material-symbols-outlined align-middle mr-2">save</span>
                            Actualizar Usuario
                        </button>
                        <a href="configuracion.php" 
                           class="px-6 py-3 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 font-medium">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Sección 4: Lista de Usuarios -->
        <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm border border-border-light dark:border-border-dark">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Usuarios Registrados</h2>
                <span class="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Total: <?php echo count($usuarios); ?> usuarios
                </span>
            </div>
            
            <?php if (count($usuarios) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Usuario</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">RUC</th>
                                <th class="py-3 px-4">Rol</th>
                                <th class="py-3 px-4">Estado</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr class="border-b border-border-light dark:border-border-dark hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-4 px-4 font-medium"><?php echo $usuario['id_trabajador']; ?></td>
                                    <td class="py-4 px-4"><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                                    <td class="py-4 px-4"><?php echo htmlspecialchars($usuario['EMAIL']); ?></td>
                                    <td class="py-4 px-4"><?php echo htmlspecialchars($usuario['RUC'] ?? 'N/A'); ?></td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php echo $usuario['rol_ID'] == 1 ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : 
                                                   ($usuario['rol_ID'] <= 3 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 
                                                   'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'); ?>">
                                            <?php echo htmlspecialchars($usuario['nombre_rol'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php echo $usuario['estado'] == 'activo' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'; ?>">
                                            <?php echo ucfirst($usuario['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <!-- Editar -->
                                            <a href="configuracion.php?editar=<?php echo $usuario['id_trabajador']; ?>" 
                                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                               title="Editar usuario">
                                                <span class="material-symbols-outlined">edit</span>
                                            </a>
                                            
                                            <!-- Eliminar (excepto gerente principal) -->
                                            <?php if ($usuario['id_trabajador'] != 1): ?>
                                                <a href="configuracion.php?eliminar=<?php echo $usuario['id_trabajador']; ?>" 
                                                   class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                   title="Eliminar usuario"
                                                   onclick="return confirm('¿Está seguro de eliminar al usuario <?php echo addslashes($usuario['nombre_usuario']); ?>?')">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-400 cursor-not-allowed" title="No se puede eliminar al gerente principal">
                                                    <span class="material-symbols-outlined">lock</span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-text-secondary-light dark:text-text-secondary-dark">
                    <span class="material-symbols-outlined text-4xl mb-2">group_off</span>
                    <p>No se encontraron usuarios</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    // Confirmación para eliminar
    function confirmarEliminacion(nombre) {
        return confirm(`¿Está seguro de eliminar al usuario ${nombre}? Esta acción marcará al usuario como inactivo.`);
    }
</script>
</body>
</html>
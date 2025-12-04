<?php
session_start();

// CONFIGURACIÓN DE LA BASE DE DATOS
$servername = "localhost";
$username_db = "root";  // Variable renombrada para evitar conflicto
$password_db = "mysql";
$dbname = "miBase1";

// Inicializar variables de sesión
if (!isset($_SESSION['intentos_fallidos'])) {
    $_SESSION['intentos_fallidos'] = [];
}
if (!isset($_SESSION['tokens_recuperacion'])) {
    $_SESSION['tokens_recuperacion'] = [];
}
if (!isset($_SESSION['usuarios_bloqueados'])) {
    $_SESSION['usuarios_bloqueados'] = [];
}

// FUNCIÓN PARA GENERAR TOKEN
function generarToken() {
    return bin2hex(random_bytes(16));
}

// FUNCIÓN PARA VERIFICAR SI USUARIO ESTÁ BLOQUEADO
function usuarioBloqueado($username) {
    return isset($_SESSION['usuarios_bloqueados'][$username]) && 
           $_SESSION['usuarios_bloqueados'][$username] > time();
}

// FUNCIÓN PARA BLOQUEAR USUARIO
function bloquearUsuario($username) {
    $_SESSION['usuarios_bloqueados'][$username] = time() + 1800; // 30 minutos
    $token = generarToken();
    $_SESSION['tokens_recuperacion'][$username] = $token;
    return $token;
}

// FUNCIÓN PARA OBTENER INTENTOS FALLIDOS
function obtenerIntentos($username) {
    return isset($_SESSION['intentos_fallidos'][$username]) ? $_SESSION['intentos_fallidos'][$username] : 0;
}

// FUNCIÓN PARA INCREMENTAR INTENTOS
function incrementarIntentos($username) {
    if (!isset($_SESSION['intentos_fallidos'][$username])) {
        $_SESSION['intentos_fallidos'][$username] = 0;
    }
    $_SESSION['intentos_fallidos'][$username]++;
}

// FUNCIÓN PARA RESETEAR INTENTOS
function resetearIntentos($username) {
    unset($_SESSION['intentos_fallidos'][$username]);
}

// PROCESAR FORMULARIO DE LOGIN
$mensaje_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $mensaje_error = "Por favor, complete todos los campos.";
    }
    elseif (usuarioBloqueado($username)) {
        $tiempo_restante = $_SESSION['usuarios_bloqueados'][$username] - time();
        $minutos = ceil($tiempo_restante / 60);
        $mensaje_error = "Usuario bloqueado. Tiempo restante: {$minutos} minutos. Use el token de recuperación.";
    }
    else {
        // CONEXIÓN Y CONSULTA A LA BD
        $conn = new mysqli($servername, $username_db, $password_db, $dbname);
        
        if ($conn->connect_error) {
            $mensaje_error = "Error de conexión a la base de datos: " . $conn->connect_error;
        } else {
            // Usar backticks para la columna CONTRASEÑA
            $sql = "SELECT id_trabajador, nombre_usuario, `CONTRASEÑA`, EMAIL FROM Usuarios WHERE nombre_usuario = ? AND estado = 'activo'";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    // Usuario no existe
                    incrementarIntentos($username);
                    $intentos = obtenerIntentos($username);
                    $mensaje_error = "Usuario no encontrado. Intentos: {$intentos}/3";
                    
                    if ($intentos >= 3) {
                        $token = bloquearUsuario($username);
                        $mensaje_error = "Usuario bloqueado por demasiados intentos.";
                    }
                } else {
                    $usuario = $result->fetch_assoc();
                    
                    // Verificar contraseña (texto plano)
                    if ($password !== $usuario['CONTRASEÑA']) {
                        incrementarIntentos($username);
                        $intentos = obtenerIntentos($username);
                        $mensaje_error = "Contraseña incorrecta. Intentos: {$intentos}/3";
                        
                        if ($intentos >= 3) {
                            $token = bloquearUsuario($username);
                            $mensaje_error = "Usuario bloqueado por demasiados intentos.";
                        }
                    } else {
                      // LOGIN EXITOSO
resetearIntentos($username);

// Obtener rol del usuario
$sql_rol = "SELECT r.nombre_rol, u.rol_ID FROM Usuarios u 
            JOIN Roles r ON u.rol_ID = r.rol_ID 
            WHERE u.id_trabajador = ?";
$stmt_rol = $conn->prepare($sql_rol);
$stmt_rol->bind_param("i", $usuario['id_trabajador']);
$stmt_rol->execute();
$result_rol = $stmt_rol->get_result();
$rol_info = $result_rol->fetch_assoc();

$_SESSION['user'] = [
    'id_trabajador' => $usuario['id_trabajador'],
    'username' => $usuario['nombre_usuario'],
    'email' => $usuario['EMAIL'],
    'rol_id' => $rol_info['rol_ID'],
    'rol_nombre' => $rol_info['nombre_rol'],
    'login_time' => time()
];

$stmt_rol->close();
$stmt->close();
$conn->close();

// Redirigir a productos
header('Location: productos.php');
exit;
                    }
                }
                $stmt->close();
            } else {
                $mensaje_error = "Error en la consulta SQL: " . $conn->error;
            }
            $conn->close();
        }
    }
}

// PROCESAR RECUPERACIÓN DE CONTRASEÑA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_recuperacion'])) {
    $email = trim($_POST['email_recuperacion']);
    
    if (empty($email)) {
        $mensaje_error = "Por favor, ingrese su email.";
    } else {
        $conn = new mysqli($servername, $username_db, $password_db, $dbname);
        
        if ($conn->connect_error) {
            $mensaje_error = "Error de conexión a la base de datos: " . $conn->connect_error;
        } else {
            $sql = "SELECT nombre_usuario FROM Usuarios WHERE EMAIL = ? AND estado = 'activo'";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $usuario = $result->fetch_assoc();
                    $usuario_encontrado = $usuario['nombre_usuario'];
                    
                    $token = generarToken();
                    $_SESSION['tokens_recuperacion'][$usuario_encontrado] = $token;
                    $mensaje_error = "Token de recuperación generado: <strong>{$token}</strong><br><br>Usuario asociado: <strong>{$usuario_encontrado}</strong><br><br>Copie el token y úselo para desbloquear su cuenta.";
                    
                    // Mostrar automáticamente el formulario de desbloqueo
                    echo '<script>setTimeout(function() { mostrarFormulario("desbloqueo"); }, 100);</script>';
                } else {
                    $mensaje_error = "Email no encontrado en el sistema.";
                }
                $stmt->close();
            } else {
                $mensaje_error = "Error en la consulta de email: " . $conn->error;
            }
            $conn->close();
        }
    }
}

// PROCESAR DESBLOQUEO CON TOKEN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token_desbloqueo'])) {
    $username = trim($_POST['username_desbloqueo']);
    $token_ingresado = trim($_POST['token_desbloqueo']);
    
    if (empty($username) || empty($token_ingresado)) {
        $mensaje_error = "Por favor, complete todos los campos.";
    }
    elseif (isset($_SESSION['tokens_recuperacion'][$username]) && 
        $_SESSION['tokens_recuperacion'][$username] === $token_ingresado) {
        
        unset($_SESSION['usuarios_bloqueados'][$username]);
        unset($_SESSION['intentos_fallidos'][$username]);
        unset($_SESSION['tokens_recuperacion'][$username]);
        
        $mensaje_error = "Cuenta desbloqueada exitosamente. Ya puede iniciar sesión.";
        
        echo '<script>setTimeout(function() { mostrarFormulario("login"); }, 2000);</script>';
    } else {
        $mensaje_error = "Token incorrecto o usuario no existe. Verifique el usuario y token.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuckyPets - Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7dbb2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #f9e4c7;
            width: 400px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0px 0px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-container img {
            width: 120px;
            height: 120px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        h2 {
            color: #6b3d09;
            margin: 0;
            font-size: 32px;
        }
        p {
            color: #6b3d09;
            margin: 5px 0 25px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border-radius: 10px;
            border: 1px solid #c7a585;
            font-size: 15px;
            box-sizing: border-box;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #6b3d09;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            cursor: pointer;
            transition: background 0.3s;
            margin-bottom: 10px;
        }
        .login-btn:hover {
            background: #4e2c06;
        }
        .forgot {
            margin-top: 15px;
            color: #6b3d09;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }
        .forgot:hover {
            color: #4e2c06;
        }
        .mensaje-error {
            color: #d63031;
            margin-bottom: 15px;
            padding: 12px;
            background: #ffeaea;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .mensaje-exito {
            color: #00b894;
            margin-bottom: 15px;
            padding: 12px;
            background: #e8fff4;
            border: 1px solid #b3ffd9;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-section {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 10px;
        }
        .toggle-form {
            color: #6b3d09;
            cursor: pointer;
            margin: 10px 0;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <img src="img/pets_login.jpg" alt="LuckyPets" onerror="this.style.display='none'">
    <h2>LuckyPets</h2>
    <div style="margin: 9px 0; text-align: center;">
        <img src="../util/welcomepic.png" alt="Logo LuckyPets" 
             style="max-width: 200px; height: auto; border-radius: 10px;"
             onerror="this.style.display='none'">
    </div>
    <p>Bienvenido de nuevo</p>

    <?php if (!empty($mensaje_error)): ?>
        <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
    <?php endif; ?>

    <!-- FORMULARIO DE LOGIN -->
    <form id="loginForm" method="POST" action="">
        <input type="text" name="username" placeholder="Usuario" required 
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" class="login-btn">Iniciar Sesión</button>
    </form>

    <div class="toggle-form" onclick="mostrarFormulario('recuperacion')">
        ¿Olvidé mi contraseña?
    </div>

    <!-- FORMULARIO DE RECUPERACIÓN -->
    <form id="recuperacionForm" class="form-section" method="POST" action="">
        <h3 style="color: #6b3d09; margin-bottom: 15px;">Recuperar Contraseña</h3>
        <input type="email" name="email_recuperacion" placeholder="Ingrese su email" required>
        <button type="submit" class="login-btn">Generar Token</button>
        <div class="toggle-form" onclick="mostrarFormulario('login')">← Volver al login</div>
    </form>

    <!-- FORMULARIO DE DESBLOQUEO -->
    <form id="desbloqueoForm" class="form-section" method="POST" action="">
        <h3 style="color: #6b3d09; margin-bottom: 15px;">Desbloquear Cuenta</h3>
        <input type="text" name="username_desbloqueo" placeholder="Usuario" required>
        <input type="text" name="token_desbloqueo" placeholder="Token de recuperación" required>
        <button type="submit" class="login-btn">Desbloquear Cuenta</button>
        <div class="toggle-form" onclick="mostrarFormulario('login')">← Volver al login</div>
    </form>
</div>

<script>
function mostrarFormulario(tipo) {
    // Ocultar todos los formularios
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('recuperacionForm').style.display = 'none';
    document.getElementById('desbloqueoForm').style.display = 'none';
    
    // Mostrar solo el formulario seleccionado
    if (tipo === 'login') {
        document.getElementById('loginForm').style.display = 'block';
    } else if (tipo === 'recuperacion') {
        document.getElementById('recuperacionForm').style.display = 'block';
    } else if (tipo === 'desbloqueo') {
        document.getElementById('desbloqueoForm').style.display = 'block';
    }
}

// Mostrar automáticamente el formulario de desbloqueo si hay token generado
window.onload = function() {
    <?php if (isset($_SESSION['tokens_recuperacion']) && !empty($_SESSION['tokens_recuperacion'])): ?>
        // Si hay tokens de recuperación, mostrar formulario de desbloqueo
        mostrarFormulario('desbloqueo');
    <?php endif; ?>
};
</script>

</body>
</html>
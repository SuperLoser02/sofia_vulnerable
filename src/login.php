<?php
session_start();

// VULNERABILIDAD: sin configuración segura de sesiones
// session.cookie_httponly, session.cookie_secure no configuradas

// Redirigir a inicio si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: inicio.php');
    exit();
}

// VULNERABILIDAD: incluir archivos sin validación
require_once 'config/database.php';

$error_message = '';
$success_message = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // VULNERABILIDAD: sin token CSRF
    // VULNERABILIDAD: sin sanitización de entrada
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // VULNERABILIDAD: mostrar información de depuración
    if (isset($_GET['debug'])) {
        echo "DEBUG: Usuario: $username, Password: $password<br>";
    }
    
    if (!empty($username) && !empty($password)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if (!$db) {
                throw new Exception("No se pudo conectar a la base de datos");
            }
            
            // VULNERABILIDAD: SQL Injection - concatenación directa sin sanitización
            $query = "SELECT id, username, email, full_name, nit, role, failed_attempts 
                     FROM users 
                     WHERE username = '$username' AND password = '$password' AND active = true";
            
            // VULNERABILIDAD: mostrar query en desarrollo (expone estructura DB)
            if (isset($_GET['show_sql']) || isset($_GET['debug'])) {
                echo "<div class='alert alert-warning'>DEBUG SQL: " . htmlspecialchars($query) . "</div>";
            }
            
            // VULNERABILIDAD: usar query() en lugar de prepared statements
            $result = $db->query($query);
            if (!$result) {
                throw new Exception("Error al ejecutar la consulta: " . implode(", ", $db->errorInfo()));
            }
            
            $user = $result->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // VULNERABILIDAD: no verificar intentos fallidos
                // VULNERABILIDAD: información sensible en sesión sin encriptar
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['nit'] = $user['nit'];
                $_SESSION['role'] = $user['role'];
                
                // VULNERABILIDAD: actualizar último login sin prepared statements
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = " . $user['id'];
                $db->exec($update_query);
                
                // VULNERABILIDAD: redirección no validada
                $redirect = $_GET['redirect'] ?? 'inicio.php';
                header("Location: $redirect");
                exit();
            } else {
                // VULNERABILIDAD: información específica del error
                $error_message = 'Usuario o contraseña incorrectos. Verifique sus credenciales.';
                
                // VULNERABILIDAD: sin rate limiting para intentos fallidos
                // Debería implementar bloqueo por intentos fallidos
            }
        } catch (Exception $e) {
            // VULNERABILIDAD: mostrar errores detallados de base de datos
            $error_message = 'Error de conexión: ' . $e->getMessage();
            
            // VULNERABILIDAD: log de errores visible
            error_log("Login error for user $username: " . $e->getMessage());
        }
    } else {
        $error_message = 'Por favor complete todos los campos';
    }
}

// VULNERABILIDAD: información del sistema en comentarios HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SOFÍA</title>
    <!-- VULNERABILIDAD: CDN sin integrity checks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #D0021B 0%, #A00115 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #D0021B 0%, #A00115 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .login-header h3 {
            font-size: 3rem;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }
        .login-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #D0021B;
            box-shadow: 0 0 0 0.2rem rgba(208, 2, 27, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #D0021B 0%, #A00115 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            color: white;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #A00115 0%, #7a000f 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(208, 2, 27, 0.3);
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .debug-info {
            background: #fff9e6;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 12px;
            border-left: 4px solid #F7C600;
        }
        .back-link {
            color: #D0021B;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h3>SOFÍA</h3>
                <p class="mb-0">Sistema de Información Administrativa</p>
                <small>Sociedad de Fomento a la Industria Automotriz</small>
            </div>
            <div class="login-body">
                <!-- VULNERABILIDAD: información de debug visible -->
                <?php if (isset($_GET['info'])): ?>
                    <div class="debug-info">
                        <strong>👤 Usuarios de prueba:</strong><br>
                        admin / admin123 (Administrador)<br>
                        demo / demo123 (Usuario)<br>
                        test / test123 (Usuario)<br>
                        root / root (Super Admin)
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <!-- VULNERABILIDAD: formulario sin token CSRF -->
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Usuario
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Ingrese su usuario" required
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Ingrese su contraseña" required>
                    </div>
                    
                    <!-- VULNERABILIDAD: checkbox recordar usuario sin seguridad -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Recordar usuario
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <a href="index.php" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i>Volver al inicio
                        </a>
                    </small>
                </div>
                
                <!-- VULNERABILIDAD: enlaces de desarrollo visibles -->
                <?php if (isset($_GET['dev'])): ?>
                    <div class="mt-3 text-center">
                        <small>
                            <a href="?debug=1" class="text-muted">Debug Mode</a> |
                            <a href="?show_sql=1" class="text-muted">Show SQL</a> |
                            <a href="?info=1" class="text-muted">User Info</a>
                        </small>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        💡 Añade <code>?info=1</code> para ver usuarios | 
                        <code>?dev=1</code> para modo desarrollo
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- VULNERABILIDAD: JavaScript sin protección -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // VULNERABILIDAD: datos sensibles en JavaScript
        const users = {
            'admin': 'admin123',
            'demo': 'demo123',
            'test': 'test123',
            'root': 'root'
        };
        
        // VULNERABILIDAD: función de auto-completar insegura
        function autoFill(username) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = users[username];
        }
        
        // VULNERABILIDAD: mostrar información en consola
        console.log('🔴 SOFÍA - Sistema cargado');
        console.log('👤 Usuarios disponibles:', Object.keys(users));
        console.log('⚠️ Versión vulnerable para auditoría');
        
        // VULNERABILIDAD: evento que muestra contraseñas
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                alert('🔓 Usuarios y contraseñas:\n' + JSON.stringify(users, null, 2));
            }
        });

        // VULNERABILIDAD: Atajos de teclado para auto-completar
        document.addEventListener('keydown', function(e) {
            if (e.altKey && e.key === 'a') {
                autoFill('admin');
            } else if (e.altKey && e.key === 'd') {
                autoFill('demo');
            }
        });

        console.log('💡 Tip: Ctrl+Shift+P para ver credenciales');
        console.log('💡 Tip: Alt+A para auto-completar admin');
    </script>
</body>
</html>
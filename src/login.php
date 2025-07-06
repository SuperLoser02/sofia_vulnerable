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
    <title>Login - Sistema de Impuestos Nacionales</title>
    <!-- VULNERABILIDAD: CDN sin integrity checks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
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
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
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
            border-color: #3b82f6;
            box-shadow: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            color: white;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h3><i class="fas fa-university me-2"></i>SIN</h3>
                <p class="mb-0">Servicio de Impuestos Nacionales</p>
                <small>Estado Plurinacional de Bolivia</small>
            </div>
            <div class="login-body">
                <!-- VULNERABILIDAD: información de debug visible -->
                <?php if (isset($_GET['info'])): ?>
                    <div class="debug-info">
                        <strong>Usuarios de prueba:</strong><br>
                        demo / demo123 (Usuario)<br>
                        admin / admin (Administrador)<br>
                        test / test123 (Usuario)<br>
                        root / toor (Admin)
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
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Ingrese su usuario" required
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
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
                        <a href="index.php" class="text-decoration-none">
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
            </div>
        </div>
    </div>
    
    <!-- VULNERABILIDAD: JavaScript sin protección -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // VULNERABILIDAD: datos sensibles en JavaScript
        const users = {
            'demo': 'demo123',
            'admin': 'admin',
            'test': 'test123'
        };
        
        // VULNERABILIDAD: función de auto-completar insegura
        function autoFill(username) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = users[username];
        }
        
        // VULNERABILIDAD: mostrar información en consola
        console.log('Sistema de login cargado');
        console.log('Usuarios disponibles:', Object.keys(users));
        
        // VULNERABILIDAD: evento que muestra contraseñas
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'P') {
                alert('Usuarios: ' + JSON.stringify(users));
            }
        });
    </script>
</body>
</html>
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Impuestos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
</head>
<body>
    <div class="container-fluid login-container d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-4 col-lg-3">
                <div class="card login-card">
                    <div class="card-header login-header text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Sistema de Impuestos
                        </h3>
                        <p class="mb-0 mt-2">Iniciar Sesión</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Usuario
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Ingrese su usuario" required 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Contraseña
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Ingrese su contraseña" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-login btn-lg text-white">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Iniciar Sesión
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Usuario demo: <strong>demo</strong> | Contraseña: <strong>demo123</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

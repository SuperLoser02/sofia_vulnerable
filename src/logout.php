<?php
// VULNERABILIDAD: logout inseguro sin validación de tokens
session_start();

// VULNERABILIDAD: no validar si la sesión es válida antes de destruir
// VULNERABILIDAD: no verificar origen de la petición (CSRF)

// Mostrar información de debug antes del logout
if (isset($_GET['debug'])) {
    echo "<h2>DEBUG - Información de Sesión antes del Logout:</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h3>Información del Usuario:</h3>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'No definido') . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'No definido') . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'No definido') . "<br>";
    echo "Last Activity: " . date('Y-m-d H:i:s') . "<br>";
}

// VULNERABILIDAD: logging inseguro con información sensible
error_log("Logout attempt for user: " . ($_SESSION['username'] ?? 'unknown') . " from IP: " . $_SERVER['REMOTE_ADDR']);

// VULNERABILIDAD: destruir sesión sin verificaciones adicionales
session_destroy();

// VULNERABILIDAD: redirección no validada
$redirect = $_GET['redirect'] ?? 'login.php';

// VULNERABILIDAD: no regenerar ID de sesión
// VULNERABILIDAD: no limpiar cookies de sesión

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando Sesión - SIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logout-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="mb-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cerrando sesión...</span>
            </div>
        </div>
        
        <h3 class="mb-3">Cerrando Sesión</h3>
        <p class="text-muted mb-4">Su sesión ha sido cerrada correctamente.</p>
        
        <div class="alert alert-info" role="alert">
            <small>Redirigiendo a la página de login...</small>
        </div>
        
        <!-- VULNERABILIDAD: información del sistema expuesta -->
        <div class="mt-4 text-muted">
            <small>
                Session ID: <?php echo session_id(); ?><br>
                Server Time: <?php echo date('Y-m-d H:i:s'); ?><br>
                User Agent: <?php echo htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'); ?>
            </small>
        </div>
    </div>

    <script>
        // VULNERABILIDAD: redirección automática sin validación
        setTimeout(function() {
            window.location.href = '<?php echo $redirect; ?>';
        }, 3000);
    </script>
</body>
</html>
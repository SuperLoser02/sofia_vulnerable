<?php
// VULNERABILIDAD: logout inseguro sin validación de tokens
// SOFÍA - Sociedad de Fomento a la Industria Automotriz
session_start();

// VULNERABILIDAD: no validar si la sesión es válida antes de destruir
// VULNERABILIDAD: no verificar origen de la petición (CSRF)

// Mostrar información de debug antes del logout
if (isset($_GET['debug'])) {
    echo "<h2>DEBUG - Información de Sesión SOFÍA antes del Logout:</h2>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo "<h3>Información del Usuario SOFÍA:</h3>";
    echo "User ID: " . ($_SESSION['user_id'] ?? 'No definido') . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'No definido') . "<br>";
    echo "Full Name: " . ($_SESSION['full_name'] ?? 'No definido') . "<br>";
    echo "Email: " . ($_SESSION['email'] ?? 'No definido') . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'No definido') . "<br>";
    echo "NIT: " . ($_SESSION['nit'] ?? 'No definido') . "<br>";
    echo "Last Activity: " . date('Y-m-d H:i:s') . "<br>";
    echo "IP Address: " . $_SERVER['REMOTE_ADDR'] . "<br>";
    echo "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "<br>";
}

// VULNERABILIDAD: logging inseguro con información sensible
$log_message = "SOFÍA Logout - User: " . ($_SESSION['username'] ?? 'unknown') . 
               " | Full Name: " . ($_SESSION['full_name'] ?? 'N/A') .
               " | Role: " . ($_SESSION['role'] ?? 'N/A') .
               " | IP: " . $_SERVER['REMOTE_ADDR'] .
               " | Time: " . date('Y-m-d H:i:s');
error_log($log_message);

// VULNERABILIDAD: guardar información de sesión antes de destruir (sin protección)
$last_user = $_SESSION['username'] ?? 'guest';
$last_role = $_SESSION['role'] ?? 'unknown';
$last_email = $_SESSION['email'] ?? 'no-email';

// VULNERABILIDAD: destruir sesión sin verificaciones adicionales
session_destroy();

// VULNERABILIDAD: redirección no validada
$redirect = $_GET['redirect'] ?? 'login.php';

// VULNERABILIDAD: no regenerar ID de sesión
// VULNERABILIDAD: no limpiar cookies de sesión

// VULNERABILIDAD: mostrar información después del logout
if (isset($_GET['show_info'])) {
    echo "<div style='background: #ffebee; padding: 20px; border: 1px solid red;'>";
    echo "<h3>⚠️ Información de Sesión Cerrada (VULNERABLE):</h3>";
    echo "Último usuario: " . htmlspecialchars($last_user) . "<br>";
    echo "Rol: " . htmlspecialchars($last_role) . "<br>";
    echo "Email: " . htmlspecialchars($last_email) . "<br>";
    echo "IP: " . $_SERVER['REMOTE_ADDR'] . "<br>";
    echo "Hora de logout: " . date('Y-m-d H:i:s') . "<br>";
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando Sesión - SOFÍA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #D0021B 0%, #A00115 100%)
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .logout-container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.4);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .spinner-border {
            width: 3.5rem;
            height: 3.5rem;
        }
        .logo {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .system-name {
            color: #1976d2;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 0.85rem;
        }
        .vulnerable-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logo">🚗</div>
        
        <div class="system-name">
            SOFÍA
        </div>
        <p class="text-muted mb-4" style="font-size: 0.9rem;">
            Sociedad de Fomento a la Industria Automotriz
        </p>
        
        <div class="mb-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cerrando sesión...</span>
            </div>
        </div>
        
        <h4 class="mb-3">Cerrando Sesión</h4>
        <p class="text-muted mb-4">Su sesión en SOFÍA ha sido cerrada correctamente.</p>
        
        <div class="alert alert-info" role="alert">
            <small>
                <strong>✓</strong> Sesión finalizada<br>
                Redirigiendo al inicio de sesión...
            </small>
        </div>
        
        <!-- VULNERABILIDAD: información del sistema expuesta -->
        <div class="info-box text-muted">
            <small>
                <strong>Información de Sesión:</strong><br>
                Session ID: <?php echo session_id(); ?><br>
                Último Usuario: <?php echo htmlspecialchars($last_user); ?><br>
                Rol: <?php echo htmlspecialchars($last_role); ?><br>
                Server Time: <?php echo date('Y-m-d H:i:s'); ?><br>
                IP: <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
            </small>
        </div>
        
        <!-- VULNERABILIDAD: información adicional expuesta -->
        <div class="vulnerable-info">
            <small>
                <strong>⚠️ Sistema Vulnerable:</strong><br>
                User Agent: <?php echo htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50)); ?>...<br>
                Protocolo: <?php echo $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1'; ?><br>
                Método: <?php echo $_SERVER['REQUEST_METHOD'] ?? 'GET'; ?><br>
            </small>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <a href="login.php" style="text-decoration: none; color: #1976d2;">
                    Volver al inicio de sesión manualmente →
                </a>
            </small>
        </div>
    </div>

    <script>
        // VULNERABILIDAD: redirección automática sin validación
        // VULNERABILIDAD: URL de redirección no sanitizada
        setTimeout(function() {
            window.location.href = '<?php echo $redirect; ?>';
        }, 3000);
        
        // VULNERABILIDAD: información de debug en consola
        console.log('SOFÍA Logout Process');
        console.log('Last User: <?php echo $last_user; ?>');
        console.log('Role: <?php echo $last_role; ?>');
        console.log('Redirect to: <?php echo $redirect; ?>');
        console.log('Session destroyed at: <?php echo date('Y-m-d H:i:s'); ?>');
    </script>
</body>
</html>
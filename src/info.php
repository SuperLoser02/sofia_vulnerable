<?php
// ARCHIVO VULNERABLE PARA AUDITORIA - NUNCA usar en producción
// ============================================================

// VULNERABILIDAD: no verificar autenticación
session_start();

// VULNERABILIDAD: información del sistema expuesta públicamente
echo "<h1>Información del Sistema - Impuestos Bolivia</h1>";

// VULNERABILIDAD: phpinfo completo expuesto
if (isset($_GET['phpinfo'])) {
    phpinfo();
    exit();
}

// VULNERABILIDAD: variables del sistema expuestas
echo "<h2>Variables del Sistema</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Server Admin: " . ($_SERVER['SERVER_ADMIN'] ?? 'No definido') . "\n";
echo "Database Host: localhost\n";
echo "Database Name: impuestos_demo\n";
echo "Database User: postgres\n";
echo "</pre>";

// VULNERABILIDAD: mostrar variables de entorno
echo "<h2>Variables de Entorno (peligrosas)</h2>";
echo "<pre>";
foreach ($_ENV as $key => $value) {
    if (strpos(strtolower($key), 'pass') !== false || 
        strpos(strtolower($key), 'key') !== false || 
        strpos(strtolower($key), 'secret') !== false) {
        echo "$key = $value\n";
    }
}
echo "</pre>";

// VULNERABILIDAD: mostrar archivos del sistema
echo "<h2>Estructura de Archivos</h2>";
if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
    echo "<h3>Contenido de: $dir</h3>";
    echo "<pre>";
    $files = scandir($dir);
    foreach ($files as $file) {
        echo "$file\n";
    }
    echo "</pre>";
}

// VULNERABILIDAD: SQL directo sin autenticación
if (isset($_GET['sql'])) {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Ejecutor SQL Directo</h2>";
    echo "<div style='background: #ffe6e6; padding: 10px; border: 1px solid red;'>";
    echo "<strong>PELIGRO:</strong> Ejecutando SQL sin validación<br>";
    echo "Query: " . htmlspecialchars($_GET['sql']) . "<br>";
    
    try {
        $result = $db->query($_GET['sql']);
        if ($result) {
            echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
            $first = true;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($first) {
                    echo "<tr>";
                    foreach (array_keys($row) as $column) {
                        echo "<th style='padding: 5px; background: #f0f0f0;'>$column</th>";
                    }
                    echo "</tr>";
                    $first = false;
                }
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td style='padding: 5px;'>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    echo "</div>";
}

// VULNERABILIDAD: mostrar logs del sistema
if (isset($_GET['logs'])) {
    echo "<h2>Logs del Sistema</h2>";
    $log_files = [
        '/var/log/apache2/error.log',
        '/var/log/apache2/access.log',
        'error.log',
        '../error.log'
    ];
    
    foreach ($log_files as $log_file) {
        if (file_exists($log_file)) {
            echo "<h3>$log_file</h3>";
            echo "<pre>" . htmlspecialchars(tail($log_file, 20)) . "</pre>";
        }
    }
}

function tail($file, $lines = 10) {
    $handle = fopen($file, "r");
    if (!$handle) return "";
    
    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = array();
    
    while ($linecounter > 0) {
        $t = " ";
        while ($t != "\n") {
            if (fseek($handle, $pos, SEEK_END) == -1) {
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }
        $linecounter--;
        if ($beginning) {
            rewind($handle);
        }
        $text[$lines-$linecounter-1] = fgets($handle);
        if ($beginning) break;
    }
    fclose($handle);
    return array_reverse($text);
}

// VULNERABILIDAD: formulario de test sin validación
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Información - Vulnerable</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .danger { background: #ffebee; border: 1px solid #f44336; padding: 10px; margin: 10px 0; }
        .form { background: #e3f2fd; padding: 15px; margin: 10px 0; }
        input, textarea { width: 100%; padding: 5px; margin: 5px 0; }
        button { background: #f44336; color: white; padding: 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="danger">
    <h2>⚠️ SISTEMA VULNERABLE PARA AUDITORÍA ⚠️</h2>
    <p>Este archivo contiene múltiples vulnerabilidades intencionadas para demostración y aprendizaje.</p>
</div>

<div class="form">
    <h3>Panel de Pruebas de Vulnerabilidades</h3>
    
    <h4>1. Information Disclosure</h4>
    <a href="?phpinfo=1">Ver PHPInfo completo</a><br>
    <a href="?dir=.">Listar archivos directorio actual</a><br>
    <a href="?dir=..">Listar archivos directorio padre</a><br>
    <a href="?logs=1">Ver logs del sistema</a><br>
    
    <h4>2. Local File Inclusion</h4>
    <form method="GET">
        <input type="text" name="file" placeholder="Ruta del archivo (ej: /etc/passwd, config/database.php)" value="<?php echo htmlspecialchars($_GET['file'] ?? ''); ?>">
        <button type="submit">Leer Archivo</button>
    </form>
    
    <h4>3. Command Injection</h4>
    <form method="GET">
        <input type="text" name="cmd" placeholder="Comando del sistema (ej: ls -la, cat /etc/passwd)" value="<?php echo htmlspecialchars($_GET['cmd'] ?? ''); ?>">
        <button type="submit">Ejecutar Comando</button>
    </form>
    
    <h4>5. Logs del Sistema</h4>
    <form method="GET">
        <input type="hidden" name="show_logs" value="1">
        <button type="submit">Ver Logs del Sistema</button>
    </form>
    
    <h4>6. Debug del Sistema</h4>
    <form method="GET">
        <input type="hidden" name="debug_system" value="1">
        <button type="submit">Mostrar Debug Completo</button>
    </form>
</div>

<?php if (isset($_GET['show_logs'])): ?>
<div class="form">
    <h3>📋 Logs del Sistema</h3>
    <div style='background: #ffcdd2; padding: 15px; border: 1px solid #f44336; margin: 10px 0;'>
        <strong>⚠️ VULNERABILIDAD CRÍTICA:</strong> Logs del sistema expuestos sin autenticación.
    </div>
    
    <h4>📊 Estadísticas de Acceso</h4>
    <pre style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc;">
Servidor Web: <?php echo $_SERVER['SERVER_SOFTWARE']; ?>
Tiempo actual: <?php echo date('Y-m-d H:i:s'); ?>
IP del cliente: <?php echo $_SERVER['REMOTE_ADDR']; ?>
User Agent: <?php echo $_SERVER['HTTP_USER_AGENT']; ?>
Método HTTP: <?php echo $_SERVER['REQUEST_METHOD']; ?>
    </pre>
    
    <h4>🔐 Intentos de Login (Simulados)</h4>
    <pre style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc;">
2025-07-06 14:25:33 - LOGIN SUCCESS - User: demo - IP: 192.168.1.100
2025-07-06 14:24:15 - LOGIN FAILED - User: admin - Password: 123456 - IP: 192.168.1.200
2025-07-06 14:23:45 - LOGIN FAILED - User: root - Password: password - IP: 10.0.0.5
2025-07-06 14:22:10 - LOGIN SUCCESS - User: auditoria - IP: 192.168.1.150
2025-07-06 14:20:55 - SQL INJECTION ATTEMPT - Query: ' OR 1=1-- - IP: 192.168.1.200
    </pre>
    
    <h4>💾 Actividad de Base de Datos</h4>
    <pre style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc;">
SELECT * FROM users WHERE username = 'demo' AND password = 'demo123'
UPDATE users SET last_login = NOW() WHERE id = 1
SELECT COUNT(*) FROM taxpayers
SELECT * FROM tax_declarations WHERE status = 'pending'
    </pre>
</div>
<?php endif; ?>

<?php if (isset($_GET['debug_system'])): ?>
<div class="form">
    <h3>🔍 DEBUG - Estado del Sistema</h3>
    
    <h4>Estado de la Sesión:</h4>
    <pre style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc;">
    <?php 
    session_start();
    print_r($_SESSION ?? []);
    ?>
    </pre>
    
    <h4>Prueba de Conexión DB:</h4>
    <?php
    try {
        require_once 'config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            echo "✅ Conexión DB: OK<br>";
            
            $result = $db->query("SELECT COUNT(*) as count FROM users");
            $count = $result->fetch(PDO::FETCH_ASSOC);
            echo "👤 Usuarios en DB: " . $count['count'] . "<br>";
            
            $result = $db->query("SELECT COUNT(*) as count FROM taxpayers");
            $count = $result->fetch(PDO::FETCH_ASSOC);
            echo "🏢 Contribuyentes en DB: " . $count['count'] . "<br>";
            
        } else {
            echo "❌ Conexión DB: FALLO<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error DB: " . $e->getMessage() . "<br>";
    }
    ?>
    
    <h4>Archivos del Sistema:</h4>
    <?php
    $files = ['config/database.php', 'inicio.php', 'login.php', 'index.php'];
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "✅ $file existe<br>";
        } else {
            echo "❌ $file NO existe<br>";
        }
    }
    ?>
</div>
<?php endif; ?>

<div class="danger">
    <h3>Ejemplos de Explotación:</h3>
    <ul>
        <li><strong>SQL Injection:</strong> <code>SELECT * FROM users WHERE username='admin'--</code></li>
        <li><strong>File Inclusion:</strong> <code>../../../etc/passwd</code></li>
        <li><strong>Command Injection:</strong> <code>ls -la; cat /etc/passwd</code></li>
        <li><strong>Directory Traversal:</strong> <code>../config/database.php</code></li>
    </ul>
</div>

<p><a href="inicio.php">← Volver al Panel de Control</a></p>

</body>
</html>

<?php
// VULNERABILIDAD: Configuración de base de datos con credenciales hardcodeadas
// ============================================================================
// SOFÍA - Sociedad de Fomento a la Industria Automotriz
// Sistema Vulnerable para Auditoría

// VULNERABILIDAD: debug habilitado globalmente
error_reporting(E_ALL);
ini_set('display_errors', 1);

// VULNERABILIDAD: información de configuración expuesta
class Database {
    // VULNERABILIDAD: propiedades públicas con credenciales
    public $host;
    public $db_name;
    public $username;
    public $password;
    private $conn;

    public function __construct() {
        // VULNERABILIDAD: credenciales hardcodeadas y débiles
        $this->host = $_ENV['DB_HOST'] ?? 'db';
        $this->db_name = $_ENV['DB_NAME'] ?? 'sofias_demo';
        $this->username = $_ENV['DB_USER'] ?? 'admin';
        $this->password = $_ENV['DB_PASS'] ?? 'admin';
        
        // VULNERABILIDAD: mostrar credenciales en comentarios/debug
        if (isset($_GET['debug_db'])) {
            echo "<div style='background: yellow; padding: 10px; border: 1px solid red;'>";
            echo "<h3>🔧 DEBUG - Configuración SOFÍA Base de Datos:</h3>";
            echo "Host: " . $this->host . "<br>";
            echo "Database: " . $this->db_name . "<br>";
            echo "Username: " . $this->username . "<br>";
            echo "Password: " . $this->password . "<br>";
            echo "DSN: pgsql:host=" . $this->host . ";dbname=" . $this->db_name . "<br>";
            echo "<hr>";
            echo "<strong>Sistema:</strong> SOFÍA - Sociedad de Fomento a la Industria Automotriz<br>";
            echo "<strong>Módulos:</strong> Empresas Automotrices, Vehículos, Declaraciones Fiscales<br>";
            echo "</div>";
        }
    }

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "pgsql:host=" . $this->host . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // VULNERABILIDAD: configuración insegura de PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            
        } catch(PDOException $exception) {
            // VULNERABILIDAD: mostrar errores detallados de conexión
            echo "<div style='background: #ffebee; color: red; padding: 10px; border: 1px solid red; margin: 10px;'>";
            echo "<h3>❌ Error de Conexión a Base de Datos SOFÍA:</h3>";
            echo "Mensaje: " . $exception->getMessage() . "<br>";
            echo "Código: " . $exception->getCode() . "<br>";
            echo "Archivo: " . $exception->getFile() . "<br>";
            echo "Línea: " . $exception->getLine() . "<br>";
            echo "DSN: pgsql:host=" . $this->host . ";dbname=" . $this->db_name . "<br>";
            echo "Usuario: " . $this->username . "<br>";
            echo "Password: " . $this->password . "<br>";
            echo "<hr>";
            echo "<p><strong>Sugerencia:</strong> Verifique que el contenedor PostgreSQL esté corriendo.</p>";
            echo "<p><strong>Sistema:</strong> SOFÍA - Módulo de Gestión Automotriz</p>";
            echo "</div>";
            
            // Para debug, intentar mostrar más información
            if (isset($_GET['debug'])) {
                echo "<pre>";
                echo "=== STACK TRACE COMPLETO ===\n";
                var_dump($exception);
                echo "\n=== VARIABLES DE ENTORNO ===\n";
                echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'no definido') . "\n";
                echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'no definido') . "\n";
                echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'no definido') . "\n";
                echo "DB_PASS: " . ($_ENV['DB_PASS'] ?? 'no definido') . "\n";
                echo "</pre>";
            }
        }
        
        return $this->conn;
    }
    
    // VULNERABILIDAD: método para ejecutar SQL arbitrario
    public function executeRawSQL($sql) {
        try {
            if (!$this->conn) {
                $this->getConnection();
            }
            $result = $this->conn->query($sql);
            
            // VULNERABILIDAD: log de queries ejecutadas
            if (isset($_GET['sql_log'])) {
                echo "<div style='background: #e3f2fd; padding: 5px; margin: 5px; border-left: 3px solid blue;'>";
                echo "<small><strong>SQL Ejecutado:</strong> " . htmlspecialchars($sql) . "</small>";
                echo "</div>";
            }
            
            return $result;
        } catch (Exception $e) {
            // VULNERABILIDAD: mostrar query que falló
            echo "<div style='background: #fff3cd; padding: 10px; margin: 5px; border: 1px solid orange;'>";
            echo "<strong>⚠️ Error SQL en SOFÍA:</strong> " . $e->getMessage() . "<br>";
            echo "<strong>Query:</strong> " . htmlspecialchars($sql);
            echo "</div>";
            return false;
        }
    }
    
    // VULNERABILIDAD: método para obtener información del sistema
    public function getSystemInfo() {
        if (isset($_GET['sysinfo'])) {
            $info = [
                'Sistema' => 'SOFÍA - Sociedad de Fomento a la Industria Automotriz',
                'Base de Datos' => $this->db_name,
                'Host' => $this->host,
                'Usuario DB' => $this->username,
                'Password DB' => $this->password,
                'PHP Version' => phpversion(),
                'PostgreSQL Version' => $this->conn ? $this->conn->getAttribute(PDO::ATTR_SERVER_VERSION) : 'N/A',
                'Módulos' => 'Empresas, Vehículos, Declaraciones, Usuarios',
                'Servidor' => $_SERVER['SERVER_NAME'] ?? 'localhost',
                'IP Servidor' => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'
            ];
            
            echo "<div style='background: #f5f5f5; padding: 15px; margin: 10px; border: 2px solid #333;'>";
            echo "<h3>🔍 Información del Sistema SOFÍA</h3>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            foreach ($info as $key => $value) {
                echo "<tr><td><strong>$key:</strong></td><td>$value</td></tr>";
            }
            echo "</table>";
            echo "</div>";
        }
    }
}

// Clase para manejo de usuarios de SOFÍA
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $email;
    public $full_name;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // VULNERABILIDAD: autenticación con contraseñas en texto plano
    public function authenticate($username, $password) {
        // VULNERABLE: consulta sin prepared statements en algunos casos
        $query = "SELECT id, username, password, email, full_name, role FROM " . $this->table_name . " WHERE username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();
        
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // VULNERABILIDAD: comparación de contraseñas en texto plano
            if($password === $row['password']) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->full_name = $row['full_name'];
                $this->role = $row['role'];
                
                // VULNERABILIDAD: log con contraseña
                if (isset($_GET['auth_debug'])) {
                    echo "<div style='background: lightgreen; padding: 10px;'>";
                    echo "✅ Autenticación exitosa SOFÍA<br>";
                    echo "Usuario: " . $username . "<br>";
                    echo "Password: " . $password . "<br>";
                    echo "Rol: " . $this->role . "<br>";
                    echo "</div>";
                }
                
                return true;
            }
        }
        
        // VULNERABILIDAD: mensaje que revela si el usuario existe
        if ($num > 0) {
            echo "<div style='color: red;'>❌ Contraseña incorrecta para el usuario: $username</div>";
        } else {
            echo "<div style='color: red;'>❌ Usuario $username no existe en SOFÍA</div>";
        }
        
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, email=:email, full_name=:full_name, role=:role";
        
        $stmt = $this->conn->prepare($query);
        
        // VULNERABILIDAD: opción para guardar sin hash
        if (isset($_GET['no_hash']) || isset($_POST['plain_password'])) {
            // Guardar contraseña en texto plano
            $password_to_save = $this->password;
        } else {
            $password_to_save = password_hash($this->password, PASSWORD_DEFAULT);
        }
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $password_to_save);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":role", $this->role);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}

// VULNERABILIDAD: Credenciales y configuraciones expuestas en comentarios
/*
 * CONFIGURACIÓN DE PRODUCCIÓN SOFÍA
 * =====================================
 * Servidor: 10.50.10.100
 * Base de Datos: sofias_demo
 * Usuario: admin
 * Password: admin
 * Puerto: 5432
 * 
 * ACCESOS ADMINISTRATIVOS:
 * - Panel Admin: http://sofia.com.bo/admin
 * - Usuario Admin: admin / admin123
 * - Root DB: postgres / root123
 * 
 * API KEYS:
 * - Mapbox: pk_live_SOFIA1234567890abcdef
 * - Google Maps: AIzaSyD_SOFIA_KEY_123456789
 * - Backup FTP: ftp.sofia.com.bo (usuario: sofia_ftp, pass: ftp2024)
 * 
 * CONTACTOS:
 * - Desarrollador: dev@sofia.com.bo
 * - Soporte: soporte@sofia.com.bo / Tel: 4-4123456
 */
?>
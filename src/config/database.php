<?php
// VULNERABILIDAD: Configuración de base de datos con credenciales hardcodeadas
// ============================================================================

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
        $this->db_name = $_ENV['DB_NAME'] ?? 'impuestos_demo';
        $this->username = $_ENV['DB_USER'] ?? 'admin';
        $this->password = $_ENV['DB_PASS'] ?? 'admin';
        
        // VULNERABILIDAD: mostrar credenciales en comentarios/debug
        if (isset($_GET['debug_db'])) {
            echo "<div style='background: yellow; padding: 10px; border: 1px solid red;'>";
            echo "<h3>DEBUG - Configuración de Base de Datos:</h3>";
            echo "Host: " . $this->host . "<br>";
            echo "Database: " . $this->db_name . "<br>";
            echo "Username: " . $this->username . "<br>";
            echo "Password: " . $this->password . "<br>";
            echo "DSN: pgsql:host=" . $this->host . ";dbname=" . $this->db_name . "<br>";
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
            echo "<h3>Error de Conexión a Base de Datos:</h3>";
            echo "Mensaje: " . $exception->getMessage() . "<br>";
            echo "Código: " . $exception->getCode() . "<br>";
            echo "Archivo: " . $exception->getFile() . "<br>";
            echo "Línea: " . $exception->getLine() . "<br>";
            echo "DSN: pgsql:host=" . $this->host . ";dbname=" . $this->db_name . "<br>";
            echo "Usuario: " . $this->username . "<br>";
            echo "Password: " . $this->password . "<br>";
            echo "</div>";
            
            // Para debug, intentar mostrar más información
            if (isset($_GET['debug'])) {
                echo "<pre>";
                var_dump($exception);
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
            return $this->conn->query($sql);
        } catch (Exception $e) {
            echo "Error SQL: " . $e->getMessage();
            return false;
        }
    }
}

// Clase para manejo de usuarios
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate($username, $password) {
        $query = "SELECT id, username, password FROM " . $this->table_name . " WHERE username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();
        
        $num = $stmt->rowCount();
        
        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                return true;
            }
        }
        
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password";
        
        $stmt = $this->conn->prepare($query);
        
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>

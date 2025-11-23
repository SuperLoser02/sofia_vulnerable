-- Inicialización de la base de datos sofias_demo con VULNERABILIDADES INTENCIONADAS
-- ================================================================================
-- SOFÍA - Sociedad de Fomento a la Industria Automotriz
-- Base de datos vulnerable para proyecto de auditoría (PostgreSQL)

-- Crear tabla de usuarios (VULNERABILIDAD: sin hash de contraseñas)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- VULNERABLE: contraseñas en texto plano
    email VARCHAR(100),
    full_name VARCHAR(100),
    nit VARCHAR(20),
    ci VARCHAR(15),
    phone VARCHAR(15),
    address TEXT,
    role VARCHAR(20) DEFAULT 'user',
    active BOOLEAN DEFAULT TRUE,
    failed_attempts INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Crear tabla de empresas automotrices (VULNERABILIDAD: datos sensibles sin encriptar)
CREATE TABLE IF NOT EXISTS taxpayers (
    id SERIAL PRIMARY KEY,
    nit VARCHAR(20) UNIQUE NOT NULL,
    business_name VARCHAR(200) NOT NULL,
    legal_rep VARCHAR(100),
    activity VARCHAR(100),
    address TEXT,
    phone VARCHAR(15),
    email VARCHAR(100),
    tax_category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de registros automotrices (VULNERABILIDAD: datos financieros sin protección)
CREATE TABLE IF NOT EXISTS tax_declarations (
    id SERIAL PRIMARY KEY,
    taxpayer_id INTEGER REFERENCES taxpayers(id) ON DELETE CASCADE,
    period VARCHAR(7), -- YYYY-MM
    gross_income DECIMAL(15,2),
    deductions DECIMAL(15,2),
    tax_amount DECIMAL(15,2),
    status VARCHAR(20) DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de vehículos registrados (nuevo para SOFÍA)
CREATE TABLE IF NOT EXISTS vehicles (
    id SERIAL PRIMARY KEY,
    taxpayer_id INTEGER REFERENCES taxpayers(id) ON DELETE CASCADE,
    vin VARCHAR(17) UNIQUE NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    year INTEGER,
    color VARCHAR(30),
    license_plate VARCHAR(15),
    engine_number VARCHAR(50),
    chassis_number VARCHAR(50),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuarios demo (VULNERABILIDAD: contraseñas débiles y predecibles)
INSERT INTO users (username, password, email, full_name, nit, ci, role) VALUES 
('admin', 'admin123', 'admin@sofia.com.bo', 'Administrador SOFÍA', '1020304050', '12345678', 'admin'),
('demo', 'demo123', 'demo@sofia.com.bo', 'Usuario Demostración', '2030405060', '23456789', 'user'),
('juan.perez', 'password123', 'juan.perez@sofia.com.bo', 'Juan Carlos Pérez Mamani', '3040506070', '34567890', 'user'),
('test', 'test123', 'test@sofia.com.bo', 'Usuario Prueba', '4050607080', '45678901', 'user'),
('auditor', 'audit123', 'auditor@sofia.com.bo', 'Auditor Sistemas', '5060708090', '56789012', 'auditor'),
('root', 'root', 'root@sofia.com.bo', 'Super Administrador', '6070809010', '67890123', 'admin'),
('guest', '', 'guest@sofia.com.bo', 'Usuario Invitado', '7080901020', '78901234', 'guest');

-- Insertar empresas automotrices (VULNERABILIDAD: información real expuesta)
INSERT INTO taxpayers (nit, business_name, legal_rep, activity, address, phone, email, tax_category) VALUES
('10234567890', 'Importadora Automotriz Bolivia S.A.', 'María Elena Quispe Condori', 'Importación de Vehículos', 'Av. Blanco Galindo Km 4, Cochabamba', '4-4123456', 'ventas@importadora.bo', 'Gran Contribuyente'),
('20345678901', 'Concesionaria Premium Motors LTDA', 'Carlos Alberto Mamani Ticona', 'Venta de Vehículos Nuevos', 'Av. Cristo Redentor 1234, Santa Cruz', '3-3234567', 'contacto@premiummotors.bo', 'Régimen General'),
('30456789012', 'Taller Mecánico El Experto SRL', 'Ana Lucía Condori Flores', 'Servicio Técnico Automotriz', 'Calle México 567, La Paz', '2-2345678', 'taller@elexperto.bo', 'Régimen Simplificado'),
('40567890123', 'Repuestos Originales S.A.', 'Roberto Ticona Apaza', 'Venta de Repuestos', 'Zona Industrial, El Alto', '2-2456789', 'ventas@repuestos.bo', 'Régimen General'),
('50678901234', 'Lubricantes y Servicios Express EIRL', 'Patricia Mamani Cruz', 'Cambio de Aceite y Lubricación', 'Av. Petrolera Km 3, Santa Cruz', '3-3567890', 'express@lubricantes.bo', 'Régimen Simplificado');

-- Insertar registros financieros (VULNERABILIDAD: datos financieros sensibles)
INSERT INTO tax_declarations (taxpayer_id, period, gross_income, deductions, tax_amount, status) VALUES
(1, '2024-01', 2850000.50, 285000.00, 427500.00, 'approved'),
(1, '2024-02', 3120000.25, 312000.00, 468000.50, 'approved'),
(2, '2024-01', 1450000.00, 145000.00, 217500.00, 'pending'),
(2, '2024-02', 1620000.75, 162000.00, 243000.15, 'approved'),
(3, '2024-01', 450000.80, 45000.00, 67500.45, 'approved'),
(3, '2024-02', 480000.40, 48000.00, 72000.25, 'under_review'),
(4, '2024-01', 980000.60, 98000.00, 147000.30, 'approved'),
(4, '2024-02', 1050000.90, 105000.00, 157500.55, 'pending'),
(5, '2024-01', 320000.50, 32000.00, 48000.25, 'approved'),
(5, '2024-02', 380000.75, 38000.00, 57000.40, 'approved');

-- Insertar vehículos registrados (VULNERABILIDAD: datos sin validación)
INSERT INTO vehicles (taxpayer_id, vin, brand, model, year, color, license_plate, engine_number, chassis_number) VALUES
(1, '1HGBH41JXMN109186', 'Toyota', 'Land Cruiser Prado', 2024, 'Blanco', '1234-ABC', 'LC-4521-2024', 'JT-8745-2024'),
(1, '2FMDK3GC2BBB12345', 'Nissan', 'X-Trail', 2024, 'Gris', '2345-BCD', 'NS-7845-2024', 'NI-5632-2024'),
(2, '3GNDA13D76S123456', 'Chevrolet', 'Tracker', 2023, 'Rojo', '3456-CDE', 'CH-4521-2023', 'GM-8965-2023'),
(2, '4T1BF1FK8CU123456', 'Hyundai', 'Tucson', 2024, 'Negro', '4567-DEF', 'HY-7412-2024', 'HM-6523-2024'),
(3, '5FNRL5H40BB123456', 'Honda', 'CR-V', 2023, 'Azul', '5678-EFG', 'HD-8523-2023', 'HN-9874-2023'),
(4, '1G1ZD5ST8BF123456', 'Mazda', 'CX-5', 2024, 'Plata', '6789-FGH', 'MZ-7412-2024', 'MA-5632-2024'),
(5, 'WBADT43452G123456', 'Suzuki', 'Vitara', 2023, 'Verde', '7890-GHI', 'SZ-4785-2023', 'SU-8521-2023');

-- Crear tabla de logs vulnerable (VULNERABILIDAD: información sensible en logs)
CREATE TABLE IF NOT EXISTS system_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER,
    action TEXT,
    details TEXT, -- VULNERABLE: detalles sin filtrar
    ip_address VARCHAR(45),
    user_agent TEXT,
    sql_query TEXT, -- VULNERABLE: queries completas loggeadas
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar logs de ejemplo con información sensible
INSERT INTO system_logs (user_id, action, details, ip_address, user_agent, sql_query) VALUES
(1, 'login', 'Usuario admin logueado con contraseña: admin123', '192.168.1.100', 'Mozilla/5.0 Chrome', 'SELECT * FROM users WHERE username=''admin'' AND password=''admin123'''),
(2, 'admin_access', 'Admin accedió a datos de vehículos', '10.0.0.5', 'Mozilla/5.0 Firefox', 'SELECT * FROM vehicles WHERE vin LIKE ''%123%'''),
(3, 'error', 'Fallo de autenticación: password incorrecta password', '192.168.1.200', 'BadBot/1.0', 'SELECT * FROM users WHERE username=''juan.perez'' AND password=''password'''),
(1, 'vehicle_registration', 'Registro de vehículo Toyota Land Cruiser VIN: 1HGBH41JXMN109186', '172.18.0.1', 'Docker Container', 'INSERT INTO vehicles VALUES (...)'),
(2, 'financial_query', 'Consulta de ingresos mensuales - periodo 2024-01', '192.168.1.105', 'Mozilla/5.0 Safari', 'SELECT SUM(gross_income) FROM tax_declarations WHERE period=''2024-01''');

-- Crear vista para consultas rápidas (VULNERABILIDAD: exposición de datos sensibles)
CREATE OR REPLACE VIEW user_taxpayer_view AS
SELECT 
    u.username,
    u.password, -- VULNERABLE: contraseña expuesta en vista
    u.email,
    u.full_name,
    u.nit,
    u.ci,
    u.phone,
    u.address,
    u.role,
    t.business_name,
    t.activity,
    t.tax_category
FROM users u
LEFT JOIN taxpayers t ON u.nit = t.nit;

-- Crear índices (algunos innecesarios para mostrar información)
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_password ON users(password); -- VULNERABLE: índice en contraseñas
CREATE INDEX IF NOT EXISTS idx_taxpayers_nit ON taxpayers(nit);
CREATE INDEX IF NOT EXISTS idx_vehicles_vin ON vehicles(vin);

-- ============================================
-- VULNERABILIDADES IMPLEMENTADAS:
-- ============================================
-- 1. Contraseñas en texto plano
-- 2. Nombres de usuario predecibles (admin, root, test)
-- 3. Logs con información sensible sin cifrar (contraseñas visibles)
-- 4. Datos de vehículos y empresas expuestos
-- 5. Sin validación de complejidad de contraseñas
-- 6. Usuario root/admin con contraseñas obvias
-- 7. Vista con contraseñas expuestas
-- 8. Índice en columna de contraseñas
-- 9. Sin encriptación de datos sensibles (VIN, Chassis)
-- 10. Emails y teléfonos sin validación

-- VULNERABILIDAD: Comentarios con información sensible
-- Servidor de producción SOFÍA: 10.50.10.100
-- Usuario admin DB: admin / password: admin123
-- Backup server: backup.sofia.com.bo
-- API Key Mapbox: pk_live_SOFIA1234567890abcdef
-- Secret Token Autenticación: sk_sofia_1234567890abcdef
-- FTP Credentials: ftp.sofia.com.bo user: sofia_ftp pass: ftp2024
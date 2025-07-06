-- Inicialización de la base de datos impuestos_demo con VULNERABILIDADES INTENCIONADAS
-- ================================================================================

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

-- Crear tabla de contribuyentes (VULNERABILIDAD: datos sensibles sin encriptar)
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

-- Crear tabla de declaraciones (VULNERABILIDAD: datos financieros sin protección)
CREATE TABLE IF NOT EXISTS tax_declarations (
    id SERIAL PRIMARY KEY,
    taxpayer_id INTEGER REFERENCES taxpayers(id),
    period VARCHAR(7), -- YYYY-MM
    gross_income DECIMAL(15,2),
    deductions DECIMAL(15,2),
    tax_amount DECIMAL(15,2),
    status VARCHAR(20) DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuarios demo (VULNERABILIDAD: contraseñas débiles y predecibles)
INSERT INTO users (username, password, email, full_name, nit, ci, role) VALUES 
('demo', 'demo123', 'demo@impuestos.gov.bo', 'Usuario Demo', '1234567890', '12345678', 'user'),
('admin', 'admin', 'admin@impuestos.gov.bo', 'Administrador Sistema', '0987654321', '87654321', 'admin'),
('usuario1', '123456', 'user1@empresa.bo', 'Juan Pérez García', '1122334455', '11223344', 'user'),
('test', 'test', 'test@test.bo', 'Usuario Test', '5566778899', '55667788', 'user'),
('auditoria', 'audit123', 'audit@impuestos.gov.bo', 'Auditor Sistemas', '9988776655', '99887766', 'auditor'),
('guest', '', 'guest@impuestos.gov.bo', 'Usuario Invitado', '1111111111', '11111111', 'guest');

-- Insertar contribuyentes con datos sensibles (VULNERABILIDAD: información real expuesta)
INSERT INTO taxpayers (nit, business_name, legal_rep, activity, address, phone, email, tax_category) VALUES
('20234567890', 'Empresa Boliviana LTDA', 'María Elena Quispe', 'Comercio General', 'Av. El Prado 123, La Paz', '2-2345678', 'info@empresaboliviana.bo', 'Régimen General'),
('30345678901', 'Servicios Técnicos S.R.L.', 'Carlos Alberto Mamani', 'Servicios Técnicos', 'Calle Comercio 456, Santa Cruz', '3-3456789', 'contacto@servtecnicos.bo', 'Régimen Simplificado'),
('40456789012', 'Industrias del Norte S.A.', 'Ana Lucía Condori', 'Manufactura', 'Zona Industrial Norte, El Alto', '2-4567890', 'ventas@industriasnorte.bo', 'Régimen General'),
('50567890123', 'Transportes Rápidos EIRL', 'Roberto Ticona Flores', 'Transporte', 'Terminal de Buses, Cochabamba', '4-5678901', 'info@transportesrapidos.bo', 'Régimen Simplificado');

-- Insertar declaraciones con montos reales (VULNERABILIDAD: datos financieros sensibles)
INSERT INTO tax_declarations (taxpayer_id, period, gross_income, deductions, tax_amount, status) VALUES
(1, '2024-01', 850000.50, 125000.75, 112500.85, 'approved'),
(1, '2024-02', 920000.25, 138000.50, 121500.40, 'approved'),
(2, '2024-01', 450000.00, 67500.00, 59625.00, 'pending'),
(2, '2024-02', 520000.75, 78000.25, 68950.15, 'approved'),
(3, '2024-01', 1250000.80, 187500.60, 165000.45, 'approved'),
(3, '2024-02', 1180000.40, 177000.30, 156500.25, 'under_review'),
(4, '2024-01', 680000.60, 102000.45, 89750.30, 'approved'),
(4, '2024-02', 750000.90, 112500.80, 98875.55, 'pending');
('admin', 'admin', 'admin@impuestos.gov.bo', 'Administrador SIN', '9876543210', '87654321', 'admin'),
('sin_user', '12345', 'user@sin.gov.bo', 'Usuario SIN', '5555555555', '55555555', 'user'),
('test', 'test123', 'test@test.com', 'Usuario Test', '1111111111', '11111111', 'user'),
('root', 'toor', 'root@impuestos.gov.bo', 'Root User', '0000000000', '00000000', 'admin')
ON CONFLICT (username) DO NOTHING;

-- Insertar contribuyentes de ejemplo (VULNERABILIDAD: datos sin validación)
INSERT INTO taxpayers (nit, business_name, legal_rep, activity, address, phone, email, tax_category) VALUES 
('1234567890', 'Empresa Demo SA', 'Juan Pérez', 'Comercio', 'Av. 6 de Agosto #123, La Paz', '78912345', 'empresa@demo.com', 'Grande'),
('9876543210', 'PYME Bolivia SRL', 'María García', 'Servicios', 'Calle Comercio #456, Santa Cruz', '76543210', 'info@pyme.bo', 'Mediana'),
('5555555555', 'Microempresa Test', 'Carlos López', 'Manufactura', 'Zona Sur #789, Cochabamba', '65432109', 'micro@test.bo', 'Pequeña'),
('0000000000', 'Empresa Vulnerable LTDA', 'Admin Root', 'Desarrollo', 'Calle Insegura #000', '00000000', 'vulnerable@hack.me', 'Test')
ON CONFLICT (nit) DO NOTHING;

-- Insertar declaraciones de ejemplo
INSERT INTO tax_declarations (taxpayer_id, period, gross_income, deductions, tax_amount, status) VALUES 
(1, '2024-12', 150000.00, 15000.00, 33750.00, 'approved'),
(1, '2025-01', 180000.00, 18000.00, 40500.00, 'pending'),
(2, '2024-12', 85000.00, 8500.00, 19125.00, 'approved'),
(3, '2024-12', 25000.00, 2500.00, 5625.00, 'pending'),
(4, '2024-12', 999999.99, 0.00, 249999.99, 'approved');

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

-- Crear función vulnerable para login (VULNERABILIDAD: SQL Injection)
CREATE OR REPLACE FUNCTION vulnerable_login(username_input TEXT, password_input TEXT)
RETURNS TABLE(user_id INTEGER, username TEXT, email TEXT, role TEXT) AS $$
DECLARE
    query_text TEXT;
BEGIN
    -- VULNERABILIDAD: concatenación directa sin sanitización
    query_text := 'SELECT id, username, email, role FROM users WHERE username = ''' || username_input || ''' AND password = ''' || password_input || '''';
    
    -- VULNERABILIDAD: usar EXECUTE con query construida dinámicamente
    RETURN QUERY EXECUTE query_text;
END;
$$ LANGUAGE plpgsql;

-- Crear función para búsqueda vulnerable (VULNERABILIDAD: exposición de datos)
CREATE OR REPLACE FUNCTION search_users_vulnerable(search_term TEXT)
RETURNS TABLE(id INTEGER, username TEXT, password TEXT, email TEXT, full_name TEXT, nit TEXT, role TEXT) AS $$
BEGIN
    -- VULNERABILIDAD: devolver contraseñas en búsquedas
    RETURN QUERY EXECUTE 'SELECT id, username, password, email, full_name, nit, role FROM users WHERE username LIKE ''%' || search_term || '%'' OR email LIKE ''%' || search_term || '%''';
END;
$$ LANGUAGE plpgsql;

-- Crear tabla de logs vulnerable (VULNERABILIDAD: información sensible en logs)
CREATE TABLE IF NOT EXISTS system_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER,
    action TEXT,
    details TEXT, -- VULNERABLE: detalles sin filtrar
    ip_address INET,
    user_agent TEXT,
    sql_query TEXT, -- VULNERABLE: queries completas loggeadas
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar logs de ejemplo con información sensible
INSERT INTO system_logs (user_id, action, details, ip_address, user_agent, sql_query) VALUES
(1, 'login', 'Usuario demo logueado con contraseña: demo123', '192.168.1.100', 'Mozilla/5.0 Chrome', 'SELECT * FROM users WHERE username=''demo'' AND password=''demo123'''),
(2, 'admin_access', 'Admin accedió a datos sensibles', '10.0.0.5', 'Mozilla/5.0 Firefox', 'SELECT * FROM taxpayers WHERE nit LIKE ''%123%'''),
(1, 'error', 'Fallo de autenticación: password incorrecta 12345', '192.168.1.200', 'BadBot/1.0', 'SELECT * FROM users WHERE username=''demo'' AND password=''12345''');

-- VULNERABILIDAD: Crear usuario de base de datos con privilegios excesivos
-- (Se ejecutaría en la configuración real del servidor)
-- CREATE USER 'webapp'@'%' IDENTIFIED BY 'password123';
-- GRANT ALL PRIVILEGES ON impuestos_demo.* TO 'webapp'@'%';

-- VULNERABILIDAD: Comentarios con información sensible
-- Servidor de producción: 192.168.1.50
-- Usuario admin DB: postgres / password: admin123  
-- Backup server: backup.impuestos.gov.bo
-- API Key: AIzaSyB1234567890abcdef
-- Secret Token: sk_live_1234567890abcdef
CREATE OR REPLACE FUNCTION login_user(user_input TEXT, pass_input TEXT)
RETURNS TABLE(user_id INTEGER, username TEXT, role TEXT) AS $$
BEGIN
    -- VULNERABLE: concatenación directa sin sanitización
    RETURN QUERY EXECUTE 'SELECT id, username, role FROM users WHERE username = ''' 
                         || user_input || ''' AND password = ''' || pass_input || '''';
END;
$$ LANGUAGE plpgsql;

-- Crear índices (algunos innecesarios para mostrar información)
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_password ON users(password); -- VULNERABLE: índice en contraseñas
CREATE INDEX IF NOT EXISTS idx_taxpayers_nit ON taxpayers(nit);

-- Mostrar datos de ejemplo (VULNERABILIDAD: exposición de información)
SELECT 'USUARIOS REGISTRADOS:' as info;
SELECT username, password, email, role FROM users; -- VULNERABLE: muestra contraseñas

SELECT 'CONTRIBUYENTES REGISTRADOS:' as info;
SELECT nit, business_name, legal_rep, email, tax_category FROM taxpayers;

SELECT 'SISTEMA LISTO - CONTIENE VULNERABILIDADES INTENCIONADAS PARA AUDITORÍA' as status;

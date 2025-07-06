<?php
session_start();
// Si el usuario ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: inicio.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio de Impuestos Nacionales - Estado Plurinacional de Bolivia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sin-primary: #1e3a8a;
            --sin-secondary: #0066cc;
            --sin-dark: #003399;
            --sin-light: #f8f9fa;
            --sin-accent: #28a745;
            --sin-yellow: #ffc107;
            --sin-red: #dc3545;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* Header superior */
        .top-header {
            background: #1e3a8a;
            color: white;
            padding: 5px 0;
            font-size: 0.85rem;
        }

        .govt-text {
            font-weight: bold;
        }

        /* Header principal con logos */
        .main-header {
            background: white;
            padding: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .bolivia-logo {
            height: 50px;
            margin-right: 15px;
        }

        .sin-logo-right {
            height: 50px;
            max-width: 180px;
        }

        .sin-logo-text {
            color: var(--sin-primary);
            font-weight: bold;
            font-size: 1.6rem;
            margin: 0;
            line-height: 1.2;
        }

        .sin-subtitle {
            color: var(--sin-secondary);
            font-size: 0.85rem;
            margin: 0;
        }

        /* Navegación principal */
        .main-nav {
            background: var(--sin-primary);
            padding: 0;
        }

        .navbar-nav .nav-link {
            color: white !important;
            padding: 12px 18px;
            font-weight: 500;
            border-right: 1px solid rgba(255,255,255,0.1);
            transition: background-color 0.3s;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }

        /* Carousel hero */
        .hero-carousel {
            height: 350px;
            overflow: hidden;
        }

        .carousel-item img {
            height: 350px;
            object-fit: cover;
            filter: brightness(0.8);
        }

        .carousel-caption {
            background: rgba(30, 58, 138, 0.9);
            padding: 25px 30px;
            border-radius: 8px;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            max-width: 550px;
        }

        .carousel-caption h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .carousel-caption p {
            font-size: 1rem;
            margin-bottom: 15px;
        }

        /* Servicios principales */
        .services-section {
            padding: 40px 0;
            background: var(--sin-light);
        }

        .section-title {
            color: var(--sin-primary);
            font-size: 2.2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 35px;
        }

        .service-card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 25px;
            height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            background: var(--sin-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.8rem;
            color: white;
        }

        .service-icon img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .service-card h4 {
            color: var(--sin-primary);
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .service-card p {
            font-size: 0.9rem;
            line-height: 1.4;
            margin: 0;
        }

        /* Noticias */
        .news-section {
            padding: 60px 0;
            background: white;
        }

        .news-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            transition: box-shadow 0.3s;
        }

        .news-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .news-header {
            background: var(--sin-primary);
            color: white;
            padding: 15px;
            font-weight: bold;
        }

        .news-content {
            padding: 20px;
        }

        /* Contacto y footer */
        .contact-section {
            background: var(--sin-primary);
            color: white;
            padding: 40px 0;
        }

        .contact-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .phone-highlight {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--sin-yellow);
            margin: 20px 0;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .social-link {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            transition: background-color 0.3s;
        }

        .social-link:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }

        /* Footer */
        .footer {
            background: #1a237e;
            color: white;
            padding: 40px 0 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .footer-links a:hover {
            opacity: 1;
        }

        .login-btn {
            position: fixed;
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
            background: var(--sin-accent);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s;
            z-index: 1000;
        }

        .login-btn:hover {
            background: #218838;
            color: white;
            transform: translateY(-50%) scale(1.05);
        }

        @media (max-width: 768px) {
            .sin-logo-text {
                font-size: 1.3rem;
            }
            .bolivia-logo {
                height: 40px;
                margin-right: 10px;
            }
            .sin-logo-right {
                height: 40px;
                max-width: 140px;
            }
            .login-btn {
                position: static;
                transform: none;
                margin: 20px auto;
                display: block;
                text-align: center;
            }
            .main-header .col-md-4 {
                margin-top: 10px;
            }
            .hero-carousel {
                height: 280px;
            }
            .carousel-item img {
                height: 280px;
            }
            .carousel-caption {
                bottom: 30px;
                padding: 20px;
                max-width: 90%;
            }
            .carousel-caption h2 {
                font-size: 1.4rem;
            }
            .service-card {
                height: 200px;
                margin-bottom: 20px;
            }
            .service-icon {
                width: 60px;
                height: 60px;
            }
            .section-title {
                font-size: 1.8rem;
                margin-bottom: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Header superior gobierno -->
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="govt-text">ESTADO PLURINACIONAL DE BOLIVIA</span>
                </div>
                <div class="col-md-4 text-end">
                    <small>Ministerio de Economía y Finanzas Públicas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Header principal con logos -->
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <img src="https://impuestos.gob.bo/images/logoboliviahor.png" alt="Estado Plurinacional de Bolivia" class="bolivia-logo">
                        <div>
                            <h1 class="sin-logo-text">SERVICIO DE IMPUESTOS NACIONALES</h1>
                            <p class="sin-subtitle">Estado Plurinacional de Bolivia</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <img src="https://impuestos.gob.bo/images/logoSinVer.png" alt="Impuestos Nacionales" class="sin-logo-right me-3">
                        <a href="login.php" class="btn btn-success">
                            <i class="fas fa-user me-2"></i>Acceder al Sistema
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación principal -->
    <nav class="main-nav navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#servicios">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#normativa">Normativa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#transparencia">Transparencia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#oficina-virtual">Oficina Virtual</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#comunicacion">Comunicación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contactos">Contactos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Carousel Hero -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://impuestos.gob.bo/images/uploads/img_20240418233709.jpg" class="d-block w-100" alt="SIAT en Línea">
                <div class="carousel-caption">
                    <h2>SIAT en Línea - Sistema Integrado</h2>
                    <p>Facturación electrónica para todos los sectores económicos</p>
                    <a href="#servicios" class="btn btn-warning btn-lg">Conocer más</a>
                </div>
            </div>
            
            <div class="carousel-item">
                <img src="https://impuestos.gob.bo/images/uploads/img_20230830205600.jpeg" class="d-block w-100" alt="Oficina Virtual SIN">
                <div class="carousel-caption">
                    <h2>Oficina Virtual SIN</h2>
                    <p>Realiza tus trámites tributarios desde casa</p>
                    <a href="#oficina-virtual" class="btn btn-warning btn-lg">Ingresar</a>
                </div>
            </div>
            
            <div class="carousel-item">
                <img src="https://impuestos.gob.bo/images/uploads/img_20230310154152.jpg" class="d-block w-100" alt="Verificación de Facturas">
                <div class="carousel-caption">
                    <h2>Verificación de Facturas</h2>
                    <p>Verifica la autenticidad de tus facturas</p>
                    <a href="#verificacion" class="btn btn-warning btn-lg">Verificar</a>
                </div>
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Servicios Principales -->
    <section class="services-section" id="servicios">
        <div class="container">
            <h2 class="section-title">Servicios Principales</h2>
            <div class="row justify-content-center">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/01-oficina%20virtual.jpg" alt="Oficina Virtual">
                        </div>
                        <h4>Oficina Virtual</h4>
                        <p>Realiza tus trámites tributarios desde cualquier lugar</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/02-siatenlinea.jpg" alt="SIAT en Línea">
                        </div>
                        <h4>SIAT en Línea</h4>
                        <p>Sistema Integrado de Administración Tributaria</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/03-rnd.jpg" alt="Repositorio Normativa">
                        </div>
                        <h4>Repositorio Normativa</h4>
                        <p>Consulta leyes, decretos y resoluciones tributarias</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/culturatrib.jpg" alt="Cultura Tributaria">
                        </div>
                        <h4>Cultura Tributaria</h4>
                        <p>Capacitación y educación tributaria</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/05-verificador.jpg" alt="Verificador">
                        </div>
                        <h4>Verificador</h4>
                        <p>Verifica la autenticidad de facturas y documentos</p>
                    </div>
                </div>
            </div>
            
            <div class="row justify-content-center mt-3">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/06-cursos.jpg" alt="Cursos Gratuitos">
                        </div>
                        <h4>Cursos Gratuitos</h4>
                        <p>Capacitación presencial y virtual tributaria</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/07-textos.jpg" alt="Textos y Videos">
                        </div>
                        <h4>Textos y Videos</h4>
                        <p>Material informativo tributario</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/logoasistencia.jpg" alt="Asistencia en Línea">
                        </div>
                        <h4>Asistencia en Línea</h4>
                        <p>Soporte técnico y consultas en tiempo real</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/nitsobservados.jpg" alt="NITs Observados">
                        </div>
                        <h4>NITs Observados</h4>
                        <p>Consulta de NITs que no generan crédito fiscal</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <img src="https://impuestos.gob.bo/iconos/10-siat-info.jpg" alt="SIAT Información">
                        </div>
                        <h4>SIAT Información</h4>
                        <p>Guías de uso y consultas técnicas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Noticias -->
    <section class="news-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="news-card">
                        <div class="news-header">
                            <i class="fas fa-newspaper me-2"></i>NOTICIAS
                        </div>
                        <div class="news-content">
                            <h5>El presidente Luis Arce felicita a Impuestos por la implementación del SIAT en Línea</h5>
                            <p class="text-muted">El presidente destacó el avance tecnológico del Servicio de Impuestos Nacionales...</p>
                            <a href="#" class="btn btn-primary btn-sm">Leer más</a>
                            
                            <hr>
                            
                            <h6>Impuestos muestra su amplia gama de servicios digitales en La Paz Expone 2025</h6>
                            <p class="text-muted small">El SIN participa activamente en la feria tecnológica más importante del país...</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Leer más</a>
                            
                            <div class="mt-3">
                                <a href="#" class="btn btn-secondary">OTRAS NOTICIAS</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="news-card">
                        <div class="news-header">
                            <i class="fas fa-exclamation-triangle me-2"></i>¡Atención!
                        </div>
                        <div class="news-content">
                            <p><strong>Información Ley N° 1448</strong></p>
                            <p><strong>NIT Pendientes de pago en el SIN</strong></p>
                            <p><strong>Plan Único de Cuentas Tributario</strong></p>
                            <p><strong>Fiscalizadores</strong></p>
                            <p><strong>Comunicados y avisos SIN</strong></p>
                        </div>
                    </div>
                    
                    <div class="news-card mt-4">
                        <div class="news-header">
                            <i class="fas fa-building me-2"></i>Entidades Financieras
                        </div>
                        <div class="news-content text-center">
                            <p>Consulta las entidades financieras autorizadas</p>
                            <a href="#" class="btn btn-primary">Ver listado</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de contacto -->
    <section class="contact-section" id="contactos">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <div class="text-center mb-4">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z'/%3E%3C/svg%3E" alt="Teléfono">
                        </div>
                        <h3>Consultas tributarias</h3>
                        <h2>Línea gratuita</h2>
                        <div class="phone-highlight">800-10-3444</div>
                        <p><strong>Horario de atención de 08:00 a 16:30</strong></p>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h4>Nuestras redes sociales</h4>
                        <div class="social-links">
                            <a href="https://twitter.com/sinbolivia" class="social-link" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sinbolivia/" class="social-link" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.youtube.com/user/serviciodeimpuestosb" class="social-link" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="https://www.instagram.com/sinbolivia/" class="social-link" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Radio Cultura SIN</h5>
                            <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-top: 15px;">
                                <p>Escucha nuestra programación educativa</p>
                                <small>Frecuencia: 95.3 FM</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <h5>Información</h5>
                    <ul class="footer-links">
                        <li><a href="#">Información Ley N° 1448</a></li>
                        <li><a href="#">NIT Pendientes de pago en el SIN</a></li>
                        <li><a href="#">Plan Único de Cuentas Tributario</a></li>
                        <li><a href="#">Sistema Integrado de la Administración Tributaria</a></li>
                        <li><a href="#">Fiscalizadores</a></li>
                        <li><a href="#">Comunicados y avisos SIN</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Institucional</h5>
                    <ul class="footer-links">
                        <li><a href="#">Transparencia y Lucha Contra la Corrupción</a></li>
                        <li><a href="#">Oficina Virtual</a></li>
                        <li><a href="#">Gestión Institucional</a></li>
                        <li><a href="#">Enlaces de Interés</a></li>
                        <li><a href="#">Oportunidad de Trabajo</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Servicios</h5>
                    <ul class="footer-links">
                        <li><a href="#">Normativa Tributaria</a></li>
                        <li><a href="#">Servicio al Contribuyente</a></li>
                        <li><a href="#">Comunicación y prensa</a></li>
                        <li><a href="#">Contactos</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Certificaciones</h5>
                    <div class="text-center">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='60' viewBox='0 0 100 60'%3E%3Crect width='100' height='60' fill='%23ffffff' opacity='0.1'/%3E%3Ctext x='50' y='35' text-anchor='middle' fill='white' font-size='10'%3EISO 9001%3C/text%3E%3C/svg%3E" 
                             alt="Certificación ISO" class="img-fluid mb-3">
                        <p><small>Certificación de Calidad ISO 9001:2015</small></p>
                    </div>
                </div>
            </div>
            
            <hr style="border-color: rgba(255,255,255,0.2); margin: 30px 0;">
            
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-0">© 2025 Servicio de Impuestos Nacionales - Estado Plurinacional de Bolivia</p>
                    <small>Todos los derechos reservados</small>
                </div>
                <div class="col-md-4 text-end">
                    <div class="social-links">
                        <a href="https://twitter.com/sinbolivia" class="social-link" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sinbolivia/" class="social-link" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.youtube.com/user/serviciodeimpuestosb" class="social-link" target="_blank">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón flotante de acceso al sistema -->
    <a href="login.php" class="login-btn">
        <i class="fas fa-user me-2"></i>Acceder
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-rotar carousel
        let carousel = new bootstrap.Carousel(document.getElementById('heroCarousel'), {
            interval: 5000,
            wrap: true
        });

        // Smooth scrolling para navegación
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animaciones para las tarjetas de servicios
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.service-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>

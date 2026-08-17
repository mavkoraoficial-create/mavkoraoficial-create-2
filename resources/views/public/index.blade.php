@extends('layouts.app')

@section('title', 'Mavkora - Desarrollamos el Futuro de tu Empresa')

@section('content')
<!-- Ambient Glowing Orbs -->
<div class="glow-orb glow-orb-1"></div>
<div class="glow-orb glow-orb-2"></div>
<div class="glow-orb glow-orb-3"></div>

<!-- Hero Section -->
<div class="hero-container" id="home">
    <div class="hero-content">
        <!-- Top Badge -->
        <span class="hero-badge">
            <span class="badge-dot"></span>
            • Plataforma todo en uno
        </span>

        <!-- Hero Title -->
        <h1 class="hero-title">
            Desarrollamos el<br>
            <span class="gradient-text">futuro</span> de tu empresa
        </h1>

        <!-- Hero Subtitle -->
        <p class="hero-subtitle">
            Software a medida, Inteligencia Artificial, Automatización y Soporte Tecnológico para impulsar tu crecimiento.
        </p>

        <!-- CTA Buttons -->
        <div class="hero-cta-buttons">
            <a href="javascript:void(0)" class="btn-hero" data-open-modal="quote">
                Solicitar Cotización
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <a href="#services" class="btn-hero-secondary">Ver Servicios</a>
        </div>

        <!-- Checks Row -->
        <div class="hero-checks">
            <div class="check-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Soluciones a medida</span>
            </div>
            <div class="check-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Tecnología moderna</span>
            </div>
            <div class="check-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Soporte 24/7</span>
            </div>
        </div>
    </div>

    <!-- Right Column: Hero Visual Graphic -->
    <div class="hero-visual">
        <div class="floating-logo-wrapper">
            <img src="{{ asset('favicon.png') }}" alt="Mavkora Logo 3D representation">
        </div>
        <div class="hologram-beam"></div>
        <div class="pixel-particles">
            <div class="pixel-cube"></div>
            <div class="pixel-cube"></div>
            <div class="pixel-cube"></div>
            <div class="pixel-cube"></div>
            <div class="pixel-cube"></div>
        </div>
        <div class="platform-container">
            <div class="platform-ring outer"></div>
            <div class="platform-ring"></div>
            <div class="platform-ring inner"></div>
        </div>
    </div>
</div>

<!-- Stats Bar Section -->
<div class="stats-container">
    <div class="stats-grid">
        <!-- Stat 1 -->
        <div class="stat-card">
            <div class="stat-icon-wrapper">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div class="stat-info">
                <h3>+10</h3>
                <p>Servicios Especializados</p>
            </div>
        </div>
        <!-- Stat 2 -->
        <div class="stat-card">
            <div class="stat-icon-wrapper">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                </svg>
            </div>
            <div class="stat-info">
                <h3>24/7</h3>
                <p>Soporte Disponible</p>
            </div>
        </div>
        <!-- Stat 3 -->
        <div class="stat-card">
            <div class="stat-icon-wrapper">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <div class="stat-info">
                <h3>Atención</h3>
                <p>Empresarial Personalizada</p>
            </div>
        </div>
        <!-- Stat 4 -->
        <div class="stat-card">
            <div class="stat-icon-wrapper" style="background: transparent; border-color: transparent; width: 34px; height: 34px;">
                <svg width="24" height="24" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="10.5" height="10.5" fill="#f25f22"/>
                    <rect x="12.5" width="10.5" height="10.5" fill="#7fba00"/>
                    <rect y="12.5" width="10.5" height="10.5" fill="#00a4ef"/>
                    <rect x="12.5" y="12.5" width="10.5" height="10.5" fill="#ffb900"/>
                </svg>
            </div>
            <div class="stat-info">
                <h3>Microsoft 365</h3>
                <p>Partner</p>
            </div>
        </div>
    </div>
</div>

<!-- Services Section -->
<div class="services-section-wrapper" id="services">
    <div class="services-header">
        <div class="services-header-left">
            <span class="section-subtitle">Nuestros Servicios</span>
            <h2 class="section-title">Soluciones tecnológicas para cada necesidad</h2>
        </div>
        <div class="services-header-right">
            <p>Ofrecemos una amplia gama de servicios para ayudar a tu empresa a crecer, innovar y destacarse.</p>
        </div>
    </div>

    <div class="services-grid">
        <!-- Service 1: Desarrollo Web -->
        <div class="service-card" data-open-modal="web">
            <div class="service-icon-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
            </div>
            <h3>Desarrollo Web</h3>
            <p>Creamos aplicaciones web modernas, rápidas y escalables a la medida de tu negocio.</p>
            <a href="javascript:void(0)" class="service-link" data-open-modal="web">Más información &rarr;</a>
        </div>
        <!-- Service 2: Aplicaciones Móviles -->
        <div class="service-card" data-open-modal="mobile">
            <div class="service-icon-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                </svg>
            </div>
            <h3>Aplicaciones Móviles</h3>
            <p>Desarrollamos apps nativas para iOS y Android con la mejor experiencia de usuario.</p>
            <a href="javascript:void(0)" class="service-link" data-open-modal="mobile">Más información &rarr;</a>
        </div>
        <!-- Service 3: Inteligencia Artificial -->
        <div class="service-card" data-open-modal="ai">
            <div class="service-icon-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                    <path d="M12 6v12M6 12h12M7.75 7.75l8.5 8.5M7.75 16.25l8.5-8.5"></path>
                </svg>
            </div>
            <h3>Inteligencia Artificial</h3>
            <p>Automatizamos procesos, creamos asistentes inteligentes y soluciones con IA.</p>
            <a href="javascript:void(0)" class="service-link" data-open-modal="ai">Más información &rarr;</a>
        </div>
        <!-- Service 4: Infraestructura Cloud -->
        <div class="service-card" data-open-modal="cloud">
            <div class="service-icon-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
                </svg>
            </div>
            <h3>Infraestructura Cloud</h3>
            <p>Implementamos soluciones en la nube seguras, escalables y altamente disponibles.</p>
            <a href="javascript:void(0)" class="service-link" data-open-modal="cloud">Más información &rarr;</a>
        </div>
        <!-- Service 5: Ciberseguridad -->
        <div class="service-card" data-open-modal="security">
            <div class="service-icon-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <rect x="9" y="11" width="6" height="5" rx="1"></rect>
                    <path d="M10 11V9a2 2 0 0 1 4 0v2"></path>
                </svg>
            </div>
            <h3>Ciberseguridad</h3>
            <p>Protegemos tu información y sistemas con las mejores prácticas de seguridad.</p>
            <a href="javascript:void(0)" class="service-link" data-open-modal="security">Más información &rarr;</a>
        </div>
        <!-- Service 6: Soporte Técnico -->
        <div class="service-card" data-open-modal="support">
            <div class="service-icon-container">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 1-2 2h1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2H3z"></path>
                </svg>
            </div>
            <h3>Soporte Técnico</h3>
            <p>Soporte 24/7 para garantizar el correcto funcionamiento de tu infraestructura tecnológica.</p>
            <a href="javascript:void(0)" class="service-link" data-open-modal="support">Más información &rarr;</a>
        </div>
    </div>
</div>

<!-- Why Us & Process Section -->
<div class="why-us-process-wrapper" id="about">
    <div class="why-us-process-grid">
        <!-- Left: Why Choose Us -->
        <div class="why-us-container">
            <span class="section-subtitle">¿Por qué elegirnos?</span>
            <h2 class="section-title">Tu éxito es nuestra prioridad</h2>
            <div class="why-us-list">
                <!-- Check Item 1 -->
                <div class="why-check-item">
                    <div class="why-check-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="why-check-text">
                        <h4>Soluciones personalizadas</h4>
                        <p>Adaptadas a tus necesidades específicas.</p>
                    </div>
                </div>
                <!-- Check Item 2 -->
                <div class="why-check-item">
                    <div class="why-check-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="why-check-text">
                        <h4>Tecnología de vanguardia</h4>
                        <p>Usamos las mejores herramientas del mercado.</p>
                    </div>
                </div>
                <!-- Check Item 3 -->
                <div class="why-check-item">
                    <div class="why-check-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="why-check-text">
                        <h4>Seguridad garantizada</h4>
                        <p>Protegemos tus datos y tu negocio.</p>
                    </div>
                </div>
                <!-- Check Item 4 -->
                <div class="why-check-item">
                    <div class="why-check-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="why-check-text">
                        <h4>Entrega a tiempo</h4>
                        <p>Cumplimos con los plazos establecidos.</p>
                    </div>
                </div>
                <!-- Check Item 5 -->
                <div class="why-check-item">
                    <div class="why-check-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div class="why-check-text">
                        <h4>Soporte continuo</h4>
                        <p>Te acompañamos en cada paso del camino.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Process Timeline -->
        <div class="process-container">
            <span class="section-subtitle">Nuestro Proceso</span>
            <h2 class="section-title">Así trabajamos</h2>
            
            <div class="process-timeline">
                <!-- Step 1 -->
                <div class="timeline-step">
                    <div class="step-badge">1</div>
                    <div class="step-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <h4>Reunión</h4>
                    <p>Entendemos tus necesidades</p>
                </div>
                <!-- Step 2 -->
                <div class="timeline-step">
                    <div class="step-badge">2</div>
                    <div class="step-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <h4>Análisis</h4>
                    <p>Evaluamos y planteamos la solución</p>
                </div>
                <!-- Step 3 -->
                <div class="timeline-step">
                    <div class="step-badge">3</div>
                    <div class="step-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </div>
                    <h4>Diseño</h4>
                    <p>Diseñamos la solución a tu medida</p>
                </div>
                <!-- Step 4 -->
                <div class="timeline-step">
                    <div class="step-badge">4</div>
                    <div class="step-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                    </div>
                    <h4>Desarrollo</h4>
                    <p>Construimos tu solución</p>
                </div>
                <!-- Step 5 -->
                <div class="timeline-step">
                    <div class="step-badge">5</div>
                    <div class="step-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path>
                        </svg>
                    </div>
                    <h4>Entrega</h4>
                    <p>Probamos y entregamos el producto</p>
                </div>
                <!-- Step 6 -->
                <div class="timeline-step">
                    <div class="step-badge">6</div>
                    <div class="step-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                        </svg>
                    </div>
                    <h4>Soporte</h4>
                    <p>Te acompañamos siempre</p>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Technologies Section -->
<div class="tech-section-wrapper" id="tech">
    <span class="section-subtitle">Tecnologías que usamos</span>
    
    <div class="tech-row">
        <!-- Laravel -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
            </svg>
            <span>Laravel</span>
        </div>
        <!-- PHP -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#777bb4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="12" cy="12" rx="10" ry="5"></ellipse>
                <ellipse cx="12" cy="12" rx="5" ry="10" transform="rotate(30 12 12)"></ellipse>
            </svg>
            <span>PHP</span>
        </div>
        <!-- React -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#61dafb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(30 12 12)"></ellipse>
                <ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(90 12 12)"></ellipse>
                <ellipse cx="12" cy="12" rx="10" ry="4.5" transform="rotate(150 12 12)"></ellipse>
                <circle cx="12" cy="12" r="1"></circle>
            </svg>
            <span>React</span>
        </div>
        <!-- Vue.js -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#41b883" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <polygon points="12 22 2 4 6 4 12 14 18 4 22 4 12 22"></polygon>
            </svg>
            <span>Vue.js</span>
        </div>
        <!-- Node.js -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#43853d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7v10l10 5 10-5V7L12 2z"></path>
                <polyline points="2 17 12 12 22 17"></polyline>
                <line x1="12" y1="22" x2="12" y2="12"></line>
            </svg>
            <span>Node.js</span>
        </div>
        <!-- Python -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ffe873" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2H9a3 3 0 0 0-3 3v3h6V6h4a2 2 0 0 1 2 2v4h-3a3 3 0 0 0-3 3v3h6a3 3 0 0 0 3-3v-3h-6v2h-4a2 2 0 0 1-2-2V8a3 3 0 0 1 3-3h6a3 3 0 0 1 3-3z"></path>
            </svg>
            <span>Python</span>
        </div>
        <!-- MySQL -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#00758f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22c5.523 0 10-2.239 10-5V7c0-2.761-4.477-5-10-5S2 4.239 2 7v10c0 2.761 4.477 5 10 5z"></path>
                <path d="M2 7c0 2.761 4.477 5 10 5s10-2.239 10-5"></path>
                <path d="M2 12c0 2.761 4.477 5 10 5s10-2.239 10-5"></path>
            </svg>
            <span>MySQL</span>
        </div>
        <!-- AWS -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ff9900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7v10l10 5 10-5V7L12 2z"></path>
                <path d="M7 11c2 2 8 2 10 0"></path>
                <path d="M16 13l2-1-1 3"></path>
            </svg>
            <span>AWS</span>
        </div>
        <!-- Docker -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2496ed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="16" width="20" height="4" rx="1"></rect>
                <rect x="4" y="11" width="3" height="3" rx="0.5"></rect>
                <rect x="8" y="11" width="3" height="3" rx="0.5"></rect>
                <rect x="12" y="11" width="3" height="3" rx="0.5"></rect>
                <rect x="6" y="6" width="3" height="3" rx="0.5"></rect>
                <rect x="10" y="6" width="3" height="3" rx="0.5"></rect>
            </svg>
            <span>Docker</span>
        </div>
        <!-- Git -->
        <div class="tech-item-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#f05032" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <circle cx="18" cy="18" r="3"></circle>
                <circle cx="6" cy="6" r="3"></circle>
                <circle cx="6" cy="18" r="3"></circle>
                <line x1="6" y1="9" x2="6" y2="15"></line>
                <line x1="8.5" y1="15.5" x2="15.5" y2="18"></line>
            </svg>
            <span>Git</span>
        </div>
    </div>
</div>

<!-- CTA Banner Section -->
<div class="cta-banner-container">
    <div class="cta-banner-card">
        <div class="cta-banner-left">
            <h2>¿Listo para llevar tu empresa al siguiente nivel?</h2>
            <p>Hablemos de tu proyecto y construyamos juntos el futuro.</p>
        </div>
        <div class="cta-banner-right">
            <a href="#contact" class="btn-hero">
                Solicitar Cotización
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <a href="https://wa.me/{{ config('mavkora.company.whatsapp') }}?text={{ rawurlencode('Hola, me gustaría obtener más información sobre sus servicios.') }}" target="_blank" rel="noopener" class="btn-whatsapp">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.455 5.703 1.456h.008c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- Contact Form & Footer Section -->
<div class="contact-footer-wrapper" id="contact">

    <!-- Footer columns -->
    <footer class="footer-columns-container">
        <div class="footer-grid-5">
            <!-- Column 1 -->
            <div class="footer-col about-col">
                <div class="footer-logo">
                    <img src="{{ asset('favicon.png') }}" alt="Mavkora Logo">
                    <span>MAVKORA</span>
                </div>
                <p class="about-desc">Soluciones tecnológicas que impulsan el crecimiento de tu empresa.</p>
                <div class="social-links-row">
                    <!-- LinkedIn -->
                    <a href="#" class="social-icon-link" aria-label="LinkedIn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </a>
                    <!-- Facebook -->
                    <a href="#" class="social-icon-link" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="#" class="social-icon-link" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <!-- GitHub -->
                    <a href="#" class="social-icon-link" aria-label="GitHub">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Column 2: Empresa -->
            <div class="footer-col">
                <h4>Empresa</h4>
                <ul>
                    <li><a href="#about">Nosotros</a></li>
                    <li><a href="#about">Misión y Visión</a></li>
                    <li><a href="#about">Nuestro Equipo</a></li>
                </ul>
            </div>

            <!-- Column 3: Servicios -->
            <div class="footer-col">
                <h4>Servicios</h4>
                <ul>
                    <li><a href="#services">Desarrollo Web</a></li>
                    <li><a href="#services">Aplicaciones Móviles</a></li>
                    <li><a href="#services">Inteligencia Artificial</a></li>
                    <li><a href="#services">Soporte Técnico</a></li>
                </ul>
            </div>

            <!-- Column 4: Recursos -->
            <div class="footer-col">
                <h4>Recursos</h4>
                <ul>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Casos de Éxito</a></li>
                    <li><a href="#">Preguntas Frecuentes</a></li>
                </ul>
            </div>

            <!-- Column 5: Contacto -->
            <div class="footer-col contact-col">
                <h4>Contacto</h4>
                <ul>
                    <li><a href="mailto:{{ config('mavkora.company.email') }}">{{ config('mavkora.company.email') }}</a></li>
                    <li><a href="tel:{{ preg_replace('/[^\d+]/', '', config('mavkora.company.phone')) }}">{{ config('mavkora.company.phone') }}</a></li>
                    <li><span>{{ config('mavkora.company.location') }}</span></li>
                </ul>
            </div>
        </div>

        <!-- Copyright Row -->
        <div class="footer-bottom-row">
            <p>&copy; {{ date('Y') }} Mavkora. Todos los derechos reservados.</p>
            <div class="footer-bottom-links">
                <a href="#">Política de Privacidad</a>
                <a href="#">Términos y Condiciones</a>
            </div>
        </div>
    </footer>
</div>

<!-- Detailed Modals for Services -->
<!-- Modal 1: Desarrollo Web -->
<div class="modal-overlay" id="modal-web" style="display: none;">
    <div class="modal-card">
        <button class="modal-close-btn" data-close-modal>&times;</button>
        <div class="modal-grid-columns">
            <div class="modal-text-col">
                <span class="modal-subtitle">Ecosistemas Digitales</span>
                <h3 class="modal-title">Desarrollo Web a Medida</h3>
                <p class="modal-desc">
                    Nuestro enfoque en el desarrollo web combina las últimas tecnologías con un diseño visual premium y una optimización SEO sobresaliente. Construimos sitios web corporativos, plataformas e-commerce y aplicaciones SaaS robustas que no solo atraen visitas, sino que impulsan conversiones reales.
                </p>
                <div class="modal-features-list">
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Tecnologías: Laravel, React, Vue.js, MySQL, TailwindCSS</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Enfoque UX/UI personalizado, intuitivo y premium</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Optimización de velocidad y Core Web Vitals para mejor posicionamiento</span>
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-hero modal-cta-btn" data-open-modal="quote">Iniciar Proyecto</a>
            </div>
            <div class="modal-visual-col">
                <img src="{{ asset('images/service_web_detail.png') }}" alt="Desarrollo Web a Medida Ilustración">
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Aplicaciones Móviles -->
<div class="modal-overlay" id="modal-mobile" style="display: none;">
    <div class="modal-card">
        <button class="modal-close-btn" data-close-modal>&times;</button>
        <div class="modal-grid-columns">
            <div class="modal-text-col">
                <span class="modal-subtitle">Experiencias Móviles</span>
                <h3 class="modal-title">Aplicaciones Móviles</h3>
                <p class="modal-desc">
                    Diseñamos y desarrollamos aplicaciones móviles nativas y de alto rendimiento que aprovechan al máximo el hardware de los dispositivos móviles. Nos centramos en crear interfaces fluidas con transiciones intuitivas, garantizando que tus usuarios disfruten de una experiencia móvil excepcional y un rendimiento rápido.
                </p>
                <div class="modal-features-list">
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Plataformas: iOS (Swift), Android (Kotlin) e Híbridas (Flutter)</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Diseños interactivos con micro-animaciones premium</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Integración de notificaciones push, GPS y pagos seguros</span>
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-hero modal-cta-btn" data-open-modal="quote">Iniciar Proyecto</a>
            </div>
            <div class="modal-visual-col">
                <img src="{{ asset('images/service_mobile_detail.png') }}" alt="Aplicaciones Móviles Ilustración">
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Inteligencia Artificial -->
<div class="modal-overlay" id="modal-ai" style="display: none;">
    <div class="modal-card">
        <button class="modal-close-btn" data-close-modal>&times;</button>
        <div class="modal-grid-columns">
            <div class="modal-text-col">
                <span class="modal-subtitle">Futuro Inteligente</span>
                <h3 class="modal-title">Inteligencia Artificial</h3>
                <p class="modal-desc">
                    Llevamos tu negocio a la era inteligente mediante la implementación de algoritmos de Machine Learning, procesamiento del lenguaje natural y automatización de procesos. Desde agentes de soporte autónomos hasta sistemas de predicción inteligente de datos, creamos soluciones de IA hechas a la medida de tus flujos de trabajo.
                </p>
                <div class="modal-features-list">
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Agentes de soporte IA conversacionales (Chatbots inteligentes)</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Modelos predictivos y análisis inteligente de datos corporativos</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Integración de APIs de OpenAI, Google Cloud y Anthropic</span>
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-hero modal-cta-btn" data-open-modal="quote">Iniciar Proyecto</a>
            </div>
            <div class="modal-visual-col">
                <img src="{{ asset('images/service_ai_detail.png') }}" alt="Inteligencia Artificial Ilustración">
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Infraestructura Cloud -->
<div class="modal-overlay" id="modal-cloud" style="display: none;">
    <div class="modal-card">
        <button class="modal-close-btn" data-close-modal>&times;</button>
        <div class="modal-grid-columns">
            <div class="modal-text-col">
                <span class="modal-subtitle">Nube y DevOps</span>
                <h3 class="modal-title">Infraestructura Cloud</h3>
                <p class="modal-desc">
                    Migramos y administramos tu infraestructura en las nubes más robustas del mercado. Aplicamos metodologías DevOps avanzadas y arquitectura de microservicios para asegurar que tus plataformas estén disponibles el 99.9% del tiempo, optimizando recursos y automatizando despliegues.
                </p>
                <div class="modal-features-list">
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Proveedores líderes: AWS, Google Cloud, Microsoft Azure</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Kubernetes, orquestación de contenedores y dockerización</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Automatización de despliegues (CI/CD Pipelines) e infraestructuras como código</span>
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-hero modal-cta-btn" data-open-modal="quote">Iniciar Proyecto</a>
            </div>
            <div class="modal-visual-col">
                <img src="{{ asset('images/service_cloud_detail.png') }}" alt="Infraestructura Cloud Ilustración">
            </div>
        </div>
    </div>
</div>

<!-- Modal 5: Ciberseguridad -->
<div class="modal-overlay" id="modal-security" style="display: none;">
    <div class="modal-card">
        <button class="modal-close-btn" data-close-modal>&times;</button>
        <div class="modal-grid-columns">
            <div class="modal-text-col">
                <span class="modal-subtitle">Protección Total</span>
                <h3 class="modal-title">Ciberseguridad</h3>
                <p class="modal-desc">
                    Protegemos tu ecosistema digital contra ciberataques y fugas de datos. Realizamos auditorías periódicas de seguridad, pruebas de penetración y configuramos políticas robustas de cifrado e inicio de sesión para blindar tus bases de datos, redes e infraestructura informática contra amenazas externas e internas.
                </p>
                <div class="modal-features-list">
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Auditorías periódicas de seguridad y pruebas de penetración (Pentesting)</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Implementación de SSL, cifrado AES de bases de datos y firewalls avanzados</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Cumplimiento estricto de normativas internacionales de privacidad</span>
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-hero modal-cta-btn" data-open-modal="quote">Iniciar Proyecto</a>
            </div>
            <div class="modal-visual-col">
                <img src="{{ asset('images/service_security_detail.png') }}" alt="Ciberseguridad Ilustración">
            </div>
        </div>
    </div>
</div>

<!-- Modal 6: Soporte Técnico -->
<div class="modal-overlay" id="modal-support" style="display: none;">
    <div class="modal-card">
        <button class="modal-close-btn" data-close-modal>&times;</button>
        <div class="modal-grid-columns">
            <div class="modal-text-col">
                <span class="modal-subtitle">Tranquilidad 24/7</span>
                <h3 class="modal-title">Soporte Técnico</h3>
                <p class="modal-desc">
                    Ofrecemos monitoreo de servidores e infraestructura tecnológica de forma ininterrumpida las 24 horas del día. Nuestro equipo de ingenieros especializados responde de forma prioritaria ante cualquier incidente técnico para asegurar la continuidad de tus operaciones sin interrupciones costosas.
                </p>
                <div class="modal-features-list">
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Monitoreo reactivo y proactivo de servidores las 24 horas del día</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Help desk con tiempos de respuesta SLA mínimos y soporte telefónico</span>
                    </div>
                    <div class="modal-feature-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Mantenimiento periódico, backups mensuales e informes de infraestructura</span>
                    </div>
                </div>
                <a href="javascript:void(0)" class="btn-hero modal-cta-btn" data-open-modal="quote">Iniciar Proyecto</a>
            </div>
            <div class="modal-visual-col">
                <img src="{{ asset('images/service_support_detail.png') }}" alt="Soporte Técnico Ilustración">
            </div>
        </div>
    </div>
</div>
@endsection

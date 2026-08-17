@extends('layouts.app')

@section('title', 'Mavkora - Nuestro Portafolio')

@section('content')
<!-- Ambient Glowing Orbs -->
<div class="glow-orb glow-orb-1"></div>
<div class="glow-orb glow-orb-2"></div>
<div class="glow-orb glow-orb-3"></div>

<!-- Portfolio Page Layout -->
<div class="portfolio-section-wrapper" style="margin-top: 5rem; margin-bottom: 7rem;">
    <!-- Section Title Area -->
    <div class="portfolio-header" style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 4rem;">
        <span class="section-subtitle">Casos de Éxito</span>
        <h2 class="section-title" style="font-size: 3rem; margin-bottom: 1rem;">Nuestros Proyectos</h2>
        <p style="color: var(--text-secondary); max-width: 600px; line-height: 1.6; font-size: 1.1rem; text-align: center;">
            Diseñamos y construimos plataformas tecnológicas de alto nivel que resuelven problemas reales y aceleran el crecimiento empresarial.
        </p>
    </div>

    <!-- Portfolio Grid -->
    <div class="portfolio-grid">
        <!-- Project 1 -->
        <div class="project-card">
            <div class="project-image">
                <img src="{{ asset('images/conect_salud_mockup.png') }}" alt="Conect Salud app mockup">
            </div>
            <div class="project-info">
                <div class="project-title-row">
                    <h3>Conect Salud</h3>
                    <span class="project-badge">Salud</span>
                </div>
                <p>Plataforma de conexión entre pacientes y profesionales de la salud. Incluye videollamadas en tiempo real, agendamiento inteligente de citas y pasarela de pago para consultas médicas virtuales de forma simplificada.</p>
                <a href="javascript:void(0)" class="project-link" data-open-modal="quote">Cotizar similar &rarr;</a>
            </div>
        </div>
        <!-- Project 2 -->
        <div class="project-card">
            <div class="project-image">
                <img src="{{ asset('images/sistema_erp_mockup.png') }}" alt="Sistema ERP mockup">
            </div>
            <div class="project-info">
                <div class="project-title-row">
                    <h3>Sistema ERP</h3>
                    <span class="project-badge">Empresarial</span>
                </div>
                <p>Sistema de gestión empresarial para control de procesos y recursos. Integra módulos de compras, facturación electrónica, inventario optimizado y generación de reportes financieros automáticos para empresas en crecimiento.</p>
                <a href="javascript:void(0)" class="project-link" data-open-modal="quote">Cotizar similar &rarr;</a>
            </div>
        </div>
        <!-- Project 3 -->
        <div class="project-card">
            <div class="project-image">
                <img src="{{ asset('images/sistema_pos_mockup.png') }}" alt="Sistema POS mockup">
            </div>
            <div class="project-info">
                <div class="project-title-row">
                    <h3>Sistema POS</h3>
                    <span class="project-badge">Retail</span>
                </div>
                <p>Sistema de punto de venta moderno, rápido y fácil de usar. Optimizado para comercios físicos con soporte para lector de código de barras, facturación simplificada en caja y sincronización de datos en la nube 24/7.</p>
                <a href="javascript:void(0)" class="project-link" data-open-modal="quote">Cotizar similar &rarr;</a>
            </div>
        </div>
        <!-- Project 4 -->
        <div class="project-card">
            <div class="project-image">
                <img src="{{ asset('images/crm_empresarial_mockup.png') }}" alt="CRM Empresarial mockup">
            </div>
            <div class="project-info">
                <div class="project-title-row">
                    <h3>CRM Empresarial</h3>
                    <span class="project-badge">CRM</span>
                </div>
                <p>Gestión de clientes y oportunidades en una sola plataforma. Permite administrar el embudo de ventas de forma interactiva, automatizar recordatorios y realizar seguimiento detallado de clientes potenciales con analítica integrada.</p>
                <a href="javascript:void(0)" class="project-link" data-open-modal="quote">Cotizar similar &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection

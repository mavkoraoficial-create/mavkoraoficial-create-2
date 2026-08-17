<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mavkora')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <!-- Premium Vanilla CSS Design -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('public.home') }}" class="nav-brand">
                <img src="{{ asset('favicon.png') }}" alt="Mavkora Logo">
                <span>MAVKORA</span>
            </a>
            
            <div class="nav-menu">
                <a href="{{ route('public.home') }}" class="nav-link">Inicio</a>
                <a href="{{ route('public.home') }}#services" class="nav-link">Servicios</a>
                <a href="{{ route('public.portfolio') }}" class="nav-link">Portafolio</a>
                <a href="{{ route('public.home') }}#tech" class="nav-link">Tecnologías</a>
                <a href="{{ route('public.home') }}#about" class="nav-link">Nosotros</a>
                <a href="javascript:void(0)" class="nav-link" data-open-modal="quote">Contacto</a>
            </div>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn-login">Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="btn-nav-logout">Cerrar Sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-login">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <footer class="footer">
        <p>&copy; {{ date('Y') }} Mavkora. Todos los derechos reservados.</p>
    </footer>

    <!-- Global Modal: Solicitar Cotización -->
    <div class="modal-overlay" id="modal-quote" style="display: none;">
        <div class="modal-card">
            <button class="modal-close-btn" data-close-modal>&times;</button>
            <div class="modal-grid-columns">
                <div class="modal-text-col">
                    <span class="modal-subtitle">Hablemos</span>
                    <h3 class="modal-title">Inicia tu proyecto con nosotros</h3>
                    <p class="modal-desc">
                        Déjanos tus datos y nos pondremos en contacto contigo lo antes posible para darte una cotización a tu medida.
                    </p>

                    <form class="contact-form-layout" style="width: 100%;" id="quote-form" action="{{ route('public.quote') }}" method="POST" novalidate>
                        @csrf
                        {{-- Trampa antispam: oculta para personas, irresistible para bots. --}}
                        <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">

                        <div class="form-row-grid">
                            <div class="form-item-wrapper">
                                <label for="c_name">Nombre completo</label>
                                <input type="text" id="c_name" name="name" placeholder="Ej. Juan Pérez" required>
                            </div>
                            <div class="form-item-wrapper">
                                <label for="c_email">Correo electrónico</label>
                                <input type="email" id="c_email" name="email" placeholder="Ej. juan@empresa.com" required>
                            </div>
                        </div>
                        <div class="form-row-grid" style="margin-top: 1rem;">
                            <div class="form-item-wrapper">
                                <label for="c_phone">Teléfono de contacto</label>
                                <input type="tel" id="c_phone" name="phone" placeholder="Ej. +57 300 123 4567">
                            </div>
                            <div class="form-item-wrapper">
                                <label for="c_services">Servicio de interés</label>
                                <select id="c_services" name="service" class="contact-form-select">
                                    @foreach (config('mavkora.services') as $key => $service)
                                        <option value="{{ $key }}">{{ $service['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-item-wrapper msg-area" style="margin-top: 1rem; width: 100%;">
                            <label for="c_msg">Detalles de tu proyecto</label>
                            <textarea id="c_msg" name="message" rows="4" placeholder="Describe brevemente lo que tu empresa necesita..." required style="width: 100%;"></textarea>
                        </div>

                        <div id="quote-feedback" role="status" aria-live="polite" style="display:none;margin-top:1rem;padding:0.85rem 1rem;border-radius:10px;font-size:0.9rem;line-height:1.45;"></div>

                        <button type="submit" class="btn-hero submit-form-btn" style="margin-top: 1.5rem; width: 100%;">Enviar Mensaje</button>
                    </form>
                </div>
                <div class="modal-visual-col">
                    <img src="{{ asset('images/service_support_detail.png') }}" alt="Inicia tu proyecto con nosotros Ilustración">
                </div>
            </div>
        </div>
    </div>

    <!-- Global Modals Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalOpenTriggers = document.querySelectorAll('[data-open-modal]');
            const modalCloseTriggers = document.querySelectorAll('[data-close-modal]');
            const modalOverlays = document.querySelectorAll('.modal-overlay');

            const openModal = (modalId) => {
                // Close any currently open modals first
                modalOverlays.forEach(overlay => {
                    overlay.style.display = 'none';
                });
                
                const modal = document.getElementById(`modal-${modalId}`);
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.classList.add('no-scroll');
                }
            };

            const closeModal = (modal) => {
                modal.style.display = 'none';
                const anyOpen = Array.from(modalOverlays).some(ov => ov.style.display === 'flex');
                if (!anyOpen) {
                    document.body.classList.remove('no-scroll');
                }
            };

            // Bind Open Triggers
            modalOpenTriggers.forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const modalId = trigger.getAttribute('data-open-modal');
                    openModal(modalId);
                });
            });

            // Bind Close Triggers
            modalCloseTriggers.forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    const modal = trigger.closest('.modal-overlay');
                    if (modal) closeModal(modal);
                });
            });

            // Click outside card to close
            modalOverlays.forEach(overlay => {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        closeModal(overlay);
                    }
                });
            });

            // Envío del formulario de cotización.
            // Se hace por fetch y no con un POST normal porque el formulario
            // vive dentro de un modal: recargar la página lo cerraría y el
            // visitante nunca vería la confirmación.
            const quoteForm = document.getElementById('quote-form');
            const feedback = document.getElementById('quote-feedback');

            if (quoteForm && feedback) {
                const showFeedback = (message, ok) => {
                    feedback.textContent = message;
                    feedback.style.display = 'block';
                    feedback.style.background = ok ? 'rgba(34, 197, 94, 0.12)' : 'rgba(239, 68, 68, 0.12)';
                    feedback.style.border = '1px solid ' + (ok ? 'rgba(34, 197, 94, 0.45)' : 'rgba(239, 68, 68, 0.45)');
                    feedback.style.color = ok ? '#4ade80' : '#f87171';
                };

                quoteForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const submitBtn = quoteForm.querySelector('button[type="submit"]');
                    const originalLabel = submitBtn.textContent;

                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Enviando...';
                    feedback.style.display = 'none';

                    try {
                        const response = await fetch(quoteForm.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: new FormData(quoteForm),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (response.ok) {
                            showFeedback(data.message || '¡Gracias! Te contactaremos muy pronto.', true);
                            quoteForm.reset();
                        } else if (response.status === 422 && data.errors) {
                            showFeedback(Object.values(data.errors).flat().join(' '), false);
                        } else if (response.status === 429) {
                            showFeedback('Has enviado demasiadas solicitudes seguidas. Espera un minuto e inténtalo de nuevo.', false);
                        } else {
                            showFeedback('No pudimos enviar tu solicitud. Escríbenos a {{ config('mavkora.company.email') }} y te atendemos.', false);
                        }
                    } catch (error) {
                        showFeedback('Hubo un problema de conexión. Revisa tu internet e inténtalo de nuevo.', false);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalLabel;
                    }
                });
            }
        });
    </script>
</body>
</html>

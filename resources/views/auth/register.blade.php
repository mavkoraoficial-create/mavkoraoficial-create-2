@extends('adminlte::master')

@section('adminlte_css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=Fira+Code:wght@400;500&display=swap');

        body.register-page {
            background: #010207 !important;
            min-height: 100vh !important;
            padding: 0 !important;
            margin: 0 !important;
            font-family: 'Inter', sans-serif !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow-x: hidden !important;
            position: relative;
        }

        /* Ambient background neon glows */
        .ambient-mesh {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .mesh-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(140px);
            opacity: 0.16;
            animation: float-blob 22s infinite ease-in-out;
        }

        .mesh-blob-1 {
            top: -10%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, #3b82f6 0%, transparent 80%);
            animation-duration: 25s;
        }

        .mesh-blob-2 {
            bottom: -10%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #db2777 0%, transparent 80%);
            animation-duration: 18s;
        }

        @keyframes float-blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -45px) scale(1.05); }
        }

        /* Container & Card */
        .custom-auth-box {
            max-width: none !important;
            width: 100% !important;
            padding: 2rem !important;
            margin: 0 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            z-index: 1;
        }

        @media (max-width: 576px) {
            .custom-auth-box {
                padding: 1rem !important;
            }
        }

        .login-main-card {
            width: 100%;
            max-width: 1100px;
            background: rgba(8, 11, 26, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 2.5rem;
            backdrop-filter: blur(30px);
            box-shadow: 0 35px 90px rgba(0, 0, 0, 0.65), inset 0 1px 2px rgba(255,255,255,0.05);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 660px;
            position: relative;
        }

        @media (max-width: 992px) {
            .login-main-card {
                grid-template-columns: 1fr;
                max-width: 500px;
                min-height: auto;
            }
        }

        /* Left Section: Immersive Cyber-Branding Panel */
        .login-showcase-panel {
            padding: 4.5rem 3.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(5, 8, 22, 0.98) 0%, rgba(2, 4, 11, 0.94) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 992px) {
            .login-showcase-panel {
                display: none;
            }
        }

        /* Fine Cyber Grid fading edges */
        .showcase-grid-bg {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.005) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.005) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 1;
            mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
        }

        /* Ambient colored nebula inside showcase */
        .showcase-nebula {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, rgba(139, 92, 246, 0.08) 50%, transparent 80%);
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        /* Bounding UI Frames */
        .hud-bracket {
            position: absolute;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(129, 140, 248, 0.25);
            pointer-events: none;
            z-index: 10;
        }
        .bracket-tl { top: 25px; left: 25px; border-right: none; border-bottom: none; }
        .bracket-tr { top: 25px; right: 25px; border-left: none; border-bottom: none; }
        .bracket-bl { bottom: 25px; left: 25px; border-right: none; border-top: none; }
        .bracket-br { bottom: 25px; right: 25px; border-left: none; border-top: none; }

        /* Crosshair guides */
        .reactor-crosshair-h {
            position: absolute;
            width: 380px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.1), transparent);
            pointer-events: none;
            z-index: 1;
        }
        .reactor-crosshair-v {
            position: absolute;
            width: 1px;
            height: 380px;
            background: linear-gradient(180deg, transparent, rgba(99, 102, 241, 0.1), transparent);
            pointer-events: none;
            z-index: 1;
        }

        /* Core logo frame */
        .logo-frame-wrapper {
            position: relative;
            width: 280px;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            z-index: 5;
        }

        .logo-pulsing-circle {
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            border: 1px solid rgba(99, 102, 241, 0.12);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.08);
            animation: ring-pulse 6s ease-in-out infinite;
        }

        .logo-core-img {
            z-index: 10;
            animation: float-logo 5s ease-in-out infinite;
        }

        .logo-core-img img {
            height: 160px;
            width: auto;
            filter: drop-shadow(0 15px 45px rgba(99, 102, 241, 0.6));
        }

        @keyframes float-logo {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes ring-pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.06); opacity: 1; }
        }

        /* Typography & layout elements */
        .showcase-brand-name-huge {
            font-family: 'Outfit', sans-serif !important;
            font-size: 3.5rem !important;
            font-weight: 900 !important;
            letter-spacing: 4px !important;
            color: #ffffff !important;
            margin: 0.5rem 0 0 0 !important;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.2));
            z-index: 5;
        }

        .showcase-divider-line {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #818cf8, transparent);
            margin: 1.5rem 0 !important;
            z-index: 5;
        }

        .showcase-tagline-huge {
            font-size: 1.05rem !important;
            color: #cbd5e1 !important;
            line-height: 1.75 !important;
            max-width: 440px;
            margin: 0 !important;
            text-align: center !important;
            z-index: 5;
        }

        .showcase-tagline-huge span {
            color: #818cf8 !important;
            font-weight: 600;
        }

        /* Right Section: Glassmorphic Form Card */
        .login-form-panel {
            padding: 4.5rem 3.5rem;
            background: rgba(3, 5, 11, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 576px) {
            .login-form-panel {
                padding: 3rem 1.75rem;
            }
        }

        .form-content-box {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 2;
        }

        .form-header-block {
            text-align: center;
            margin-bottom: 2.75rem;
        }

        .form-header-block h2 {
            font-family: 'Outfit', sans-serif !important;
            font-size: 2.5rem !important;
            font-weight: 900 !important;
            color: #ffffff !important;
            margin: 0 0 0.6rem 0 !important;
            letter-spacing: -1px !important;
            line-height: 1.2 !important;
        }

        .form-header-block h2 .accent-glow {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 50%, #f472b6 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            display: inline-block !important;
        }

        .form-header-block p {
            font-family: 'Inter', sans-serif !important;
            font-size: 1.05rem !important;
            color: #94a3b8 !important;
            margin: 0 !important;
            letter-spacing: -0.2px !important;
        }

        /* Form elements */
        .form-group-glow {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group-glow label {
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            color: #cbd5e1 !important;
            letter-spacing: 0.5px;
            text-transform: uppercase !important;
        }

        .input-relative-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon-prefix {
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            display: flex;
            align-items: center;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .input-field-glow {
            width: 100% !important;
            padding: 1.05rem 1.15rem 1.05rem 3rem !important;
            background-color: rgba(255, 255, 255, 0.02) !important;
            border: 1px solid rgba(255, 255, 255, 0.06) !important;
            border-radius: 0.85rem !important;
            color: #ffffff !important;
            font-size: 0.95rem !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            height: auto !important;
        }

        .input-field-glow::placeholder {
            color: #475569;
        }

        .input-field-glow:focus {
            outline: none !important;
            border-color: rgba(99, 102, 241, 0.5) !important;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.25) !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        .input-field-glow:focus + .input-icon-prefix {
            color: #818cf8;
        }

        .btn-eye-toggle {
            position: absolute;
            right: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .btn-eye-toggle:hover {
            color: #ffffff;
        }

        .btn-submit-glow {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: #ffffff !important;
            color: #02040a !important;
            border: none !important;
            padding: 1.1rem !important;
            border-radius: 0.85rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            cursor: pointer !important;
            font-family: 'Outfit', sans-serif !important;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.05);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            margin-top: 1rem;
        }

        .btn-submit-glow:hover {
            background: #e2e8f0 !important;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.15);
        }

        .divider-social {
            text-align: center;
            position: relative;
            margin: 2.25rem 0;
        }

        .divider-social::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.06);
            z-index: 1;
        }

        .divider-social span {
            background-color: #030514;
            padding: 0 1rem;
            color: #64748b;
            font-size: 0.85rem;
            position: relative;
            z-index: 2;
            font-family: 'Outfit', sans-serif !important;
        }

        .social-buttons-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .btn-social-chip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.85rem 1.2rem;
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 0.85rem;
            color: #cbd5e1;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            font-family: 'Outfit', sans-serif !important;
            transition: all 0.3s ease;
        }

        .btn-social-chip:hover {
            background-color: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .register-bottom-prompt {
            text-align: center;
            font-size: 0.9rem;
            color: #cbd5e1;
        }

        .register-bottom-prompt a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .register-bottom-prompt a:hover {
            color: #60a5fa;
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
    </style>
    @yield('css')
@stop

@section('classes_body', 'register-page')

@section('body')
    <!-- Background Meshes -->
    <div class="ambient-mesh">
        <div class="mesh-blob mesh-blob-1"></div>
        <div class="mesh-blob mesh-blob-2"></div>
    </div>

    <div class="custom-auth-box">
        <div class="login-main-card">
            <!-- Left Pane: Immersive Cyber-Branding Panel -->
            <div class="login-showcase-panel">
                <div class="showcase-grid-bg"></div>
                <div class="showcase-nebula"></div>
                
                <!-- Viewport HUD Corner Brackets -->
                <div class="hud-bracket bracket-tl"></div>
                <div class="hud-bracket bracket-tr"></div>
                <div class="hud-bracket bracket-bl"></div>
                <div class="hud-bracket bracket-br"></div>
                
                <!-- Crosshair guides -->
                <div class="reactor-crosshair-h"></div>
                <div class="reactor-crosshair-v"></div>
                
                <!-- Pulsing Core Frame and Logo -->
                <div class="logo-frame-wrapper">
                    <div class="logo-pulsing-circle"></div>
                    <div class="logo-core-img">
                        <img src="{{ asset('favicon.png') }}" alt="Mavkora Main Logo">
                    </div>
                </div>
                
                <!-- Branding Typography -->
                <h1 class="showcase-brand-name-huge">MAVKORA</h1>
                <div class="showcase-divider-line"></div>
                <p class="showcase-tagline-huge">Construimos soluciones tecnológicas premium que impulsan el <span>crecimiento</span> de tu negocio.</p>
            </div>
            
            <!-- Right Pane: Form Card -->
            <div class="login-form-panel">
                <div class="form-content-box">
                    <div class="form-header-block">
                        <h2>Crea tu <span class="accent-glow">cuenta</span></h2>
                        <p>Regístrate para continuar a tu panel</p>
                    </div>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger-custom">
                            <ul class="mb-0 pl-0" style="list-style: none;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('register') }}" method="post">
                        @csrf
                        
                        <div class="form-group-glow">
                            <label for="name">Nombre completo</label>
                            <div class="input-relative-wrapper">
                                <div class="input-icon-prefix">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name" class="input-field-glow" placeholder="Ingresa tu nombre completo" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group-glow">
                            <label for="email">Correo electrónico</label>
                            <div class="input-relative-wrapper">
                                <div class="input-icon-prefix">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email" class="input-field-glow" placeholder="Ingresa tu correo electrónico" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        
                        <div class="form-group-glow">
                            <label for="password">Contraseña</label>
                            <div class="input-relative-wrapper">
                                <div class="input-icon-prefix">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>
                                <input type="password" id="password" name="password" class="input-field-glow" placeholder="Crea tu contraseña" required>
                                <button type="button" class="btn-eye-toggle" id="togglePassword">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="eye-icon">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group-glow">
                            <label for="password_confirmation">Confirmar contraseña</label>
                            <div class="input-relative-wrapper">
                                <div class="input-icon-prefix">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="input-field-glow" placeholder="Confirma tu contraseña" required>
                                <button type="button" class="btn-eye-toggle" id="togglePasswordConfirm">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="eye-icon-confirm">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-submit-glow">
                            Registrarse &rarr;
                        </button>
                    </form>
                    
                    <div class="divider-social">
                        <span>o continúa con</span>
                    </div>
                    
                    <div class="social-buttons-row">
                        <a href="#" class="btn-social-chip">
                            <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                            </svg>
                            Google
                        </a>
                        <a href="#" class="btn-social-chip">
                            <svg width="18" height="18" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg">
                                <rect width="10.5" height="10.5" fill="#f25f22"/>
                                <rect x="12.5" width="10.5" height="10.5" fill="#7fba00"/>
                                <rect y="12.5" width="10.5" height="10.5" fill="#00a4ef"/>
                                <rect x="12.5" y="12.5" width="10.5" height="10.5" fill="#ffb900"/>
                            </svg>
                            Microsoft
                        </a>
                    </div>
                    
                    <div class="register-bottom-prompt">
                        <span>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Password toggle
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (togglePasswordBtn && passwordInput && eyeIcon) {
                togglePasswordBtn.addEventListener('click', () => {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    if (type === 'text') {
                        eyeIcon.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        `;
                    } else {
                        eyeIcon.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        `;
                    }
                });
            }

            // Confirm Password toggle
            const togglePasswordConfirmBtn = document.getElementById('togglePasswordConfirm');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const eyeIconConfirm = document.getElementById('eye-icon-confirm');

            if (togglePasswordConfirmBtn && passwordConfirmInput && eyeIconConfirm) {
                togglePasswordConfirmBtn.addEventListener('click', () => {
                    const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirmInput.setAttribute('type', type);
                    if (type === 'text') {
                        eyeIconConfirm.innerHTML = `
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        `;
                    } else {
                        eyeIconConfirm.innerHTML = `
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        `;
                    }
                });
            }
        });
    </script>
    @yield('js')
@stop

@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('adminlte_css')
    <style>
        html, body {
            min-height: 100vh;
        }

        body.login-page,
        body.register-page,
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
            background: #0f172a;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(4, 26, 61, 0.78), rgba(0, 74, 230, 0.52)),
                url('{{ asset('vendor/adminlte/dist/img/fundo.png') }}') no-repeat center center;
            background-size: cover;
            z-index: 0;
            transform: scale(1.03);
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 30%),
                radial-gradient(circle at bottom left, rgba(0,174,239,0.18), transparent 28%);
            pointer-events: none;
        }

        .login-box,
        .register-box {
            width: 100%;
            max-width: 1100px;
            position: relative;
            z-index: 1;
        }

        .login-page .card {
            box-shadow: none !important;
            border: none !important;
            background: transparent !important;
            margin-bottom: 0;
        }

        .auth-card {
            position: relative;
            background: rgba(255, 255, 255, 0.12);
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            display: flex;
            overflow: hidden;
            animation: fadeIn 0.8s ease-in-out;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
        }

        .auth-slide,
        .auth-form {
            flex: 1 1 50%;
        }

        .auth-slide {
            position: relative;
            min-height: 600px;
            overflow: hidden;
            background: #dbeafe;
        }

        .auth-slide::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.30), rgba(15, 23, 42, 0.05));
            z-index: 1;
            pointer-events: none;
        }

        .auth-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out, transform 8s ease;
            transform: scale(1.02);
        }

        .auth-slide img.active {
            opacity: 1;
            transform: scale(1.08);
        }

        .slide-controls {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 2;
            padding: .7rem .95rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.20);
        }

        .indicators {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        .indicator {
            width: 11px;
            height: 11px;
            background: rgba(255,255,255,0.45);
            border-radius: 50%;
            cursor: pointer;
            transition: transform .2s ease, background .3s ease, box-shadow .3s ease;
        }

        .indicator.active {
            background: #ffffff;
            transform: scale(1.15);
            box-shadow: 0 0 0 4px rgba(255,255,255,0.18);
        }

        .auth-form {
            position: relative;
            z-index: 2;
            padding: 2.5rem 2.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1.25rem;
            background: rgba(255, 255, 255, 0.94);
        }

        .auth-form .logo {
            display: flex;
            justify-content: center;
            margin-bottom: .35rem;
        }

        .auth-form .logo img {
            max-width: 150px;
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .auth-form h2 {
            color: #0f172a !important;
            margin-bottom: .2rem !important;
        }

        .auth-form p {
            margin-bottom: .25rem !important;
        }

        .auth-form form {
            display: flex;
            flex-direction: column;
            gap: .95rem;
        }

        .auth-form input {
            padding: 1rem 1rem;
            border-radius: 12px;
            border: 1px solid #d8e1ec;
            width: 100%;
            transition: border-color .3s, box-shadow .3s, transform .2s;
            font-size: .95rem;
            background: #fff;
        }

        .auth-form input:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10);
            transform: translateY(-1px);
        }

        .auth-form button {
            background: linear-gradient(135deg, #004ae6, #0075ff);
            color: #fff;
            padding: 1rem 1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .3s ease, opacity .2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            font-weight: 700;
            box-shadow: 0 12px 24px rgba(0, 74, 230, 0.20);
        }

        .auth-form button:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(0, 74, 230, 0.28);
        }

        .auth-form .footer-link {
            text-align: center;
            font-size: .92rem;
            margin-top: .2rem;
        }

        .auth-form .footer-link a {
            color: #004ae6;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-form .footer-link a:hover {
            color: #002e99;
        }

        .invalid-feedback {
            display: block;
            margin-top: -.45rem;
            font-size: .85rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 991.98px) {
            .auth-card {
                min-height: auto;
                max-width: 920px;
            }

            .auth-slide {
                min-height: 500px;
            }

            .auth-form {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            body.login-page,
            body.register-page,
            body {
                padding: .85rem;
                align-items: center;
            }

            .auth-card {
                flex-direction: column;
                border-radius: 20px;
                min-height: auto;
            }

            .auth-slide {
                display: none;
            }

            .auth-form {
                padding: 1.6rem 1.2rem;
                border-radius: 20px;
            }

            .auth-form .logo img {
                max-width: 112px;
            }

            .auth-form h2 {
                font-size: 1.2rem !important;
            }
        }
    </style>
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_body')
    <div class="auth-card">
        <div class="auth-slide">
            <img src="{{ asset('vendor/adminlte/dist/img/slide1.png') }}" class="active" alt="Slide 1">
            <!-- <img src="{{ asset('vendor/adminlte/dist/img/slide2.png') }}" alt="Slide 2"> -->
            <img src="{{ asset('vendor/adminlte/dist/img/slide3.png') }}" alt="Slide 3">
            <img src="{{ asset('vendor/adminlte/dist/img/slide4.png') }}" alt="Slide 4">

            <div class="slide-controls">
                <!-- <button class="control-btn prev">Anterior</button> -->

                <div class="indicators">
                    <div class="indicator active" data-slide="0"></div>
                    <!-- <div class="indicator" data-slide="1"></div> -->
                    <div class="indicator" data-slide="2"></div>
                    <div class="indicator" data-slide="3"></div>

                </div>

                <!-- <button class="control-btn next">Próximo</button> -->
            </div>
        </div>

        <div class="auth-form"><h2 class="text-center text-primary mb-1" style="font-size:1.4rem;font-weight:700;">Acesse o sistema</h2><p class="text-center text-muted mb-2">Entre com seu e-mail e senha para continuar.</p>
            <div class="logo">
                <img src="{{ asset('vendor/adminlte/dist/img/Nova_364_verde.jpg') }}" alt="Logo">
            </div>

            <form action="{{ $login_url }}" method="post">
                @csrf

                <input type="email" name="email" class="@error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <input type="password" name="password" class="@error('password') is-invalid @enderror"
                       placeholder="{{ __('adminlte::adminlte.password') }}">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <button type="submit">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </form>

            @if($password_reset_url)
                <div class="footer-link">
                    <a href="{{ $password_reset_url }}">
                        {{ __('adminlte::adminlte.i_forgot_my_password') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@stop

@section('adminlte_js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.auth-slide img');
            const indicators = document.querySelectorAll('.indicator');
            let current = 0;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                    indicators[i].classList.toggle('active', i === index);
                });
            }

            function nextSlide() {
                current = (current + 1) % slides.length;
                showSlide(current);
            }

            indicators.forEach(indicator => {
                indicator.addEventListener('click', () => {
                    current = parseInt(indicator.getAttribute('data-slide'));
                    showSlide(current);
                });
            });

            setInterval(nextSlide, 5000);
        });


    </script>
@stop

@section('auth_footer')
@stop

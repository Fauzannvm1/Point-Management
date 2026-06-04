<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register — Point Management</title>
    <meta name="description" content="Create your Point Management account.">
    <link rel="icon" type="image/png" href="{{ asset('storage_backup/Icon.png') }}">

    <!-- Font Icon -->
    <link rel="stylesheet" href="{{ asset('dist_login/fonts/material-icon/css/material-design-iconic-font.min.css') }}">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false }
        }
    </script>

    <!-- Full Embedded Light Mode Cyberpunk/SaaS CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --cyan: #05f2f2;
            --cyan-dark: #04cccc;
            --cyan-deeper: #039e9e;
            --cyan-muted: rgba(5, 242, 242, 0.12);
            --cyan-glow: rgba(5, 242, 242, 0.25);
            --cyan-soft: rgba(5, 242, 242, 0.06);

            --bg-page: #f0f4f8;
            --bg-card: #ffffff;
            --bg-input: #f7f9fc;
            --bg-input-focus: #ffffff;

            --border-light: #e2e8f0;
            --border-hover: rgba(5, 242, 242, 0.5);
            --border-focus: var(--cyan-dark);

            --text-primary: #1a2332;
            --text-secondary: #5a6a7e;
            --text-muted: #94a3b8;
            --text-placeholder: #a0aec0;

            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --error-text: #dc2626;

            --radius-card: 20px;
            --radius-input: 10px;
            --radius-btn: 10px;

            --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05),
                           0 20px 50px -12px rgba(0, 0, 0, 0.08),
                           0 0 0 1px rgba(5, 242, 242, 0.06);
            --shadow-card-hover: 0 8px 30px -8px rgba(0, 0, 0, 0.12),
                                 0 0 0 1px rgba(5, 242, 242, 0.10);

            --t: 0.25s ease;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif !important;
            font-size: 14px;
            line-height: 1.7;
            color: var(--text-primary);
            background-color: var(--bg-page);
            background-image: radial-gradient(rgba(5, 242, 242, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            min-height: 100vh;
            margin: 0;
        }

        img { max-width: 100%; height: auto; display: block; }
        figure { margin: 0; }
        p { margin: 0; font-size: 13px; color: var(--text-secondary); }

        h2 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            line-height: 1.3;
            color: var(--text-primary);
        }

        a {
            text-decoration: none;
            transition: var(--t);
        }

        a:focus, a:active {
            outline: none;
            text-decoration: none;
        }

        input, select, textarea {
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .main {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 16px;
            position: relative;
            overflow: hidden;
        }

        .main::before, .main::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
        }

        .main::before {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(5, 242, 242, 0.15), transparent 70%);
            top: -180px;
            left: -140px;
            animation: blobFloat 18s ease-in-out infinite alternate;
        }

        .main::after {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(5, 80, 242, 0.08), transparent 70%);
            bottom: -120px;
            right: -100px;
            animation: blobFloat 24s ease-in-out infinite alternate-reverse;
        }

        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 30px) scale(1.08); }
        }

        .signup {
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .container {
            width: 100%;
            max-width: 920px;
            margin: 0 auto;
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            position: relative;
            animation: cardEnter 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
            transition: box-shadow 0.4s ease;
        }

        .container:hover {
            box-shadow: var(--shadow-card-hover);
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--cyan-deeper), var(--cyan), var(--cyan-deeper));
            pointer-events: none;
        }

        @keyframes cardEnter {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .signup-content {
            display: flex;
            align-items: center;
            padding: 56px 0;
            gap: 0;
        }

        .signup-image {
            width: 45%;
            flex-shrink: 0;
            padding: 0 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            order: 2;
            margin: 0;
        }

        .signup-image figure {
            margin: 0;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-radius: 14px;
        }

        .signup-image figure::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(5, 242, 242, 0.05) 0%, transparent 60%);
            pointer-events: none;
            border-radius: 14px;
        }

        .signup-image img {
            width: 100%;
            border-radius: 14px;
            border: 1px solid var(--border-light);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .signup-image img:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(5, 242, 242, 0.12);
        }

        .signup-form {
            width: 55%;
            flex-shrink: 0;
            padding: 0 52px;
            order: 1;
        }

        .form-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--text-primary);
            margin-bottom: 28px;
            padding-bottom: 14px;
            position: relative;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--cyan-dark), var(--cyan));
            border-radius: 2px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-danger {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
        }

        .alert-danger ul { padding-left: 16px; margin-bottom: 0; }
        .alert-danger li { margin-bottom: 2px; }

        .register-form { width: 100%; }

        .form-group {
            position: relative;
            display: flex !important;
            align-items: center !important;
            margin-bottom: 18px;
            overflow: visible;
            transition: transform 0.2s ease;
        }

        .form-group:focus-within {
            transform: translateX(4px);
        }

        .form-group:last-child { margin-bottom: 0; }

        label {
            position: relative;
            top: auto;
            left: auto;
            transform: none;
            flex-shrink: 0;
            width: 36px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 17px;
            transition: color var(--t);
            pointer-events: none;
            background: var(--bg-input);
            border: 1px solid var(--border-light);
            border-right: none;
            border-radius: var(--radius-input) 0 0 var(--radius-input);
            margin-bottom: 0;
        }

        .form-group:focus-within > label {
            color: var(--cyan-deeper);
            border-color: var(--border-focus);
            background: var(--cyan-soft);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            flex: 1 1 auto;
            width: 100%;
            display: block;
            background: var(--bg-input);
            border: 1px solid var(--border-light);
            border-radius: 0 var(--radius-input) var(--radius-input) 0;
            padding: 10px 40px 10px 12px !important;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            transition: border-color var(--t), background var(--t), box-shadow var(--t);
        }

        select {
            flex: 1 1 auto;
            width: 100%;
            display: block;
            background: var(--bg-input);
            border: 1px solid var(--border-light);
            border-radius: 0 var(--radius-input) var(--radius-input) 0;
            padding: 10px 36px 10px 12px !important;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            cursor: pointer;
            transition: border-color var(--t), background var(--t), box-shadow var(--t);
        }

        select option {
            background: #ffffff;
            color: var(--text-primary);
        }

        .form-group.select-group::after {
            content: '▾';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            font-size: 13px;
            transition: color var(--t);
        }

        .form-group.select-group:focus-within::after { color: var(--cyan-deeper); }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: var(--text-placeholder);
        }

        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover,
        select:hover {
            border-color: var(--border-hover);
        }

        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(5, 242, 242, 0.12), 0 0 8px rgba(5, 242, 242, 0.12); }
            50%      { box-shadow: 0 0 0 4px rgba(5, 242, 242, 0.18), 0 0 12px rgba(5, 242, 242, 0.20); }
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: var(--border-focus);
            background: var(--bg-input-focus);
            animation: glowPulse 2s ease-in-out infinite;
        }

        .zmdi-eye,
        .zmdi-eye-off,
        #togglePassword {
            position: absolute !important;
            right: 12px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            cursor: pointer !important;
            color: var(--text-muted);
            font-size: 16px;
            z-index: 2;
            transition: color var(--t);
            display: inline-block !important;
            width: auto !important;
            flex: none !important;
        }

        #togglePassword:hover {
            color: var(--cyan-deeper);
        }

        .form-button {
            display: block;
            margin-top: 8px;
            overflow: visible;
        }

        .form-submit {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, var(--cyan-dark) 0%, var(--cyan-deeper) 100%);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: none;
            border-radius: var(--radius-btn);
            padding: 13px 32px;
            cursor: pointer;
            transition: transform var(--t), box-shadow var(--t), background var(--t);
            position: relative;
            overflow: hidden;
            outline: none;
            margin-top: 4px;
        }

        .form-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: linear-gradient(120deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
            transform: skewX(-20deg);
            transition: left 0.5s ease;
        }

        .form-submit:hover::before { left: 125%; }

        .form-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(5, 242, 242, 0.25), 0 2px 8px rgba(0, 0, 0, 0.08);
            background: linear-gradient(135deg, var(--cyan) 0%, var(--cyan-dark) 100%);
        }

        .form-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(5, 242, 242, 0.15);
        }

        .login-link-area {
            position: relative;
            margin-top: 24px;
            text-align: center;
        }

        .login-link-area::before {
            content: '';
            display: block;
            width: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(5, 242, 242, 0.4), transparent);
            margin: 0 auto 12px;
        }

        .login-link-area p {
            color: var(--text-secondary);
            font-size: 13px;
            margin-bottom: 6px;
        }

        .login-link-area a {
            color: var(--cyan-deeper);
            font-weight: 600;
            font-size: 13px;
            border-bottom: 1px solid transparent;
            padding-bottom: 1px;
            transition: var(--t);
        }

        .login-link-area a:hover {
            color: var(--cyan-dark);
            border-bottom-color: var(--cyan-dark);
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .signup-content { flex-direction: column; padding: 36px 0 28px; }
            .signup-form { width: 100%; padding: 0 28px; order: 1; }
            .signup-image { width: 100%; order: 2; padding: 20px 28px 12px; }
            .form-title { text-align: center; }
            .form-title::after { left: 50%; transform: translateX(-50%); }
            .form-button { text-align: center; }
            .login-link-area { text-align: center; }
        }

        @media screen and (max-width: 480px) {
            .main { padding: 20px 10px; }
            .signup-form { padding: 0 18px; }
            .form-title { font-size: 22px; }
            .form-submit { font-size: 13px; padding: 12px 20px; }
        }
        body {
            user-select: none;
            -webkit-user-select: none;
        }
        input, textarea, select, .join-code-box .mono {
            user-select: text;
            -webkit-user-select: text;
        }
    </style>
</head>
<body>

    <div class="main">
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Register</h2>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST" class="register-form" id="register-form">
                        
                            @csrf
                            <div class="form-group">
                                <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="name" id="name" placeholder="Nama Lengkap"/>
                            </div>
                            <div class="form-group">
                                <label for="username"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="username" id="username" placeholder="Username" />
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email" id="email" placeholder="Alamat Email"/>
                            </div>
                            <div class="form-group select-group">
                                <label for="role"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <select name="role" id="role" required>
                                    <option value="" disabled selected>Select your role</option>
                                    <!-- <option value="admin">Admin</option> <option value="admin">Admin</option> -->
                                    <option value="teacher">Teacher</option>
                                    <option value="student">Student</option>
                                    <!-- <option value="parent">Parent</option> -->
                                </select>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Password" style="flex: 1; padding-right: 30px;" />
                                <i class="zmdi zmdi-eye" id="togglePassword" style="cursor: pointer; position: absolute; right: 10px;"></i>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                                <label for="password_confirmation"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi Password" style="flex: 1; padding-right: 30px;" />
                                <i class="zmdi zmdi-eye" id="togglePasswordConfirmation" style="cursor: pointer; position: absolute; right: 10px;"></i>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signup" id="signup" class="form-submit" value="Register"/>
                            </div>
                            <div class="login-link-area">
                                <p>Sudah punya akun?</p>
                                <a href="{{ url ('/login') }}">LOG IN</a>
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="{{ asset('dist_login/images/signup-image.jpg') }}" alt="sing up image"></figure>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <script src="{{ asset('dist_login/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('dist_login/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleVisibility(inputId, iconId) {
            let input = document.getElementById(inputId);
            let icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('zmdi-eye');
                icon.classList.add('zmdi-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('zmdi-eye-off');
                icon.classList.add('zmdi-eye');
            }
        }

        document.getElementById('togglePassword').addEventListener('click', function () {
            toggleVisibility('password', 'togglePassword');
        });

        document.getElementById('togglePasswordConfirmation').addEventListener('click', function () {
            toggleVisibility('password_confirmation', 'togglePasswordConfirmation');
        });
    </script>
    <script>
        // Prevent highlighting text globally except on specific inputs
        document.addEventListener('selectstart', function(e) {
        const tag = e.target.tagName;
        if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
            e.preventDefault();
        }
        });
        // Prevent accidental back navigation on mobile
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
        history.go(1);
        };
    </script>
</body>
</html>

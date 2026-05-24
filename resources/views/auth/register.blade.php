<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register — Point Management</title>
    <meta name="description" content="Create your Point Management account.">

    <!-- Font Icon -->
    <link rel="stylesheet" href="{{ asset ('dist_login/fonts/material-icon/css/material-design-iconic-font.min.css') }}">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        cyan: { accent: '#05f2f2', dark: '#04cccc', deeper: '#039e9e' },
                        light: { page: '#f0f4f8', card: '#ffffff' }
                    }
                }
            }
        }
    </script>

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset ('dist_login/css/style.css') }}">

    <style>
        /* Page-specific overrides for register - Light Mode */
        body {
            background-color: #f0f4f8;
            background-image: radial-gradient(rgba(5, 242, 242, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .signup-content {
            gap: 0;
        }

        .signup-image figure {
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

        .form-group {
            transition: transform 0.2s ease;
        }

        .form-group:focus-within {
            transform: translateX(4px);
        }

        @keyframes glowPulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(5, 242, 242, 0.12), 0 0 8px rgba(5, 242, 242, 0.12); }
            50%      { box-shadow: 0 0 0 4px rgba(5, 242, 242, 0.18), 0 0 12px rgba(5, 242, 242, 0.20); }
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            animation: glowPulse 2s ease-in-out infinite;
        }

        .login-link-area {
            position: relative;
        }

        .login-link-area::before {
            content: '';
            display: block;
            width: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(5, 242, 242, 0.4), transparent);
            margin: 0 auto 12px;
        }

        .alert-danger ul {
            margin-bottom: 0;
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
                                    <option value="admin">Admin</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="student">Student</option>
                                    <option value="parent">Parent</option>
                                </select>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Password" style="flex: 1; padding-right: 30px;" />
                                <i class="zmdi zmdi-eye" id="togglePassword" style="cursor: pointer; position: absolute; right: 10px;"></i>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password_confirmation" id="password" placeholder="Konfirmasi Password" style="flex: 1; padding-right: 30px;" />
                                <i class="zmdi zmdi-eye" id="togglePassword" style="cursor: pointer; position: absolute; right: 10px;"></i>
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
                        <figure><img src="{{ asset ('dist_login/images/signup-image.jpg') }}" alt="sing up image"></figure>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <script src="{{ asset ('dist_login/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset ('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            let passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('zmdi-eye');
                this.classList.add('zmdi-eye-off');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('zmdi-eye-off');
                this.classList.add('zmdi-eye');
            }
        });
    </script>
</body>
</html>

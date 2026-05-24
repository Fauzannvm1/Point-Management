<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Log In — Point Management</title>
    <meta name="description" content="Sign in to your Point Management account.">

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
        /* Page-specific overrides for login - Light Mode */
        body {
            background-color: #f0f4f8;
            background-image: radial-gradient(rgba(5, 242, 242, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .signin-content {
            gap: 0;
        }

        .signin-image figure {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
        }

        .signin-image figure::after {
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

        input[type="email"]:focus,
        input[type="password"]:focus {
            animation: glowPulse 2s ease-in-out infinite;
        }

        .register-link-area {
            position: relative;
        }

        .register-link-area::before {
            content: '';
            display: block;
            width: 60px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(5, 242, 242, 0.4), transparent);
            margin: 0 auto 12px;
        }
    </style>
</head>

<body>

    <div class="main">
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="{{ asset ('dist_login/images/signin-image.jpg') }}" alt="sing up image"></figure>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">LOG IN</h2>
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{session('error')}}
                                </div>
                            @endif

                        <form action="/proseslogin" method="POST" class="register-form" id="login-form">
                            @csrf
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="email" name="email" id="email" placeholder="Alamat Email" />
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; position: relative;">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="Password" style="flex: 1; padding-right: 30px;" />
                                <i class="zmdi zmdi-eye" id="togglePassword" style="cursor: pointer; position: absolute; right: 10px;"></i>
                            </div>
                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="Log in" />
                            </div>
                            
                        </form>
                        <div class="register-link-area">
                            <p>Belum punya akun?</p>
                            <a href="/register">Register</a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <script src="{{ asset ('dist_login/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset ('dist_login/js/main.js') }}"></script>
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
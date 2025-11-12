<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản - Beauty Shop</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .register-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .register-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .register-body {
            padding: 40px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            color: white;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .invalid-feedback {
            font-size: 13px;
            margin-top: 5px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .password-toggle:hover {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <h2><i class="fas fa-user-plus"></i> Đăng Ký Tài Khoản</h2>
                <p>Tạo tài khoản để mua sắm mỹ phẩm</p>
            </div>
            
            <!-- Body -->
            <div class="register-body">
                <!-- Flash Messages -->
                <?php if (isset($errors['general']) && !empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php foreach ($errors['general'] as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Register Form -->
                <form method="POST" action="<?= base_url('auth/register') ?>" id="registerForm" novalidate>
                    
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username *
                        </label>
                        <input 
                            type="text" 
                            class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                            id="username" 
                            name="username"
                            value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                            placeholder="Nhập username (4-20 ký tự)"
                            required
                            minlength="4"
                            maxlength="20"
                            pattern="[a-zA-Z0-9_]+"
                        >
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php foreach ($errors['username'] as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">Chỉ chứa chữ cái, số và dấu gạch dưới</small>
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input 
                            type="email" 
                            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                            id="email" 
                            name="email"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                            placeholder="Nhập email của bạn"
                            required
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php foreach ($errors['email'] as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Mật khẩu *
                        </label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                id="password" 
                                name="password"
                                placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                                required
                                minlength="6"
                            >
                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php foreach ($errors['password'] as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock"></i> Xác nhận mật khẩu *
                        </label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                id="confirm_password" 
                                name="confirm_password"
                                placeholder="Nhập lại mật khẩu"
                                required
                            >
                            <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php foreach ($errors['confirm_password'] as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Phone (Optional) -->
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone"></i> Số điện thoại
                        </label>
                        <input 
                            type="text" 
                            class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                            id="phone" 
                            name="phone"
                            value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                            placeholder="Nhập số điện thoại (tùy chọn)"
                            pattern="[0-9]{10,11}"
                        >
                        <?php if (isset($errors['phone'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php foreach ($errors['phone'] as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <small class="text-muted">VD: 0901234567</small>
                    </div>
                    
                    <!-- Address (Optional) -->
                    <div class="mb-4">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Địa chỉ
                        </label>
                        <textarea 
                            class="form-control" 
                            id="address" 
                            name="address"
                            rows="2"
                            placeholder="Nhập địa chỉ của bạn (tùy chọn)"
                        ><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus"></i> Đăng Ký
                    </button>
                </form>
                
                <!-- Login Link -->
                <div class="login-link">
                    <p class="mb-0">
                        Đã có tài khoản? 
                        <a href="<?= base_url('auth/login') ?>">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập ngay
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Form Validation
        const form = document.getElementById('registerForm');
        
        form.addEventListener('submit', function(event) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Check if passwords match
            if (password !== confirmPassword) {
                event.preventDefault();
                confirmPasswordInput.classList.add('is-invalid');
                
                // Show error message
                let errorDiv = confirmPasswordInput.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    confirmPasswordInput.parentNode.appendChild(errorDiv);
                }
                errorDiv.innerHTML = '<div>Mật khẩu xác nhận không khớp</div>';
            }
            
            // Check HTML5 validation
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
        
        // Remove invalid class on input
        const inputs = form.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>


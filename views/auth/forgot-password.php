<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - Beauty Shop</title>
    
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
        
        .forgot-password-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        
        .forgot-password-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .forgot-password-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .forgot-password-header i {
            font-size: 50px;
            margin-bottom: 15px;
        }
        
        .forgot-password-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .forgot-password-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .forgot-password-body {
            padding: 40px;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .info-box i {
            color: #2196F3;
            margin-right: 10px;
        }
        
        .info-box p {
            margin: 0;
            color: #1976D2;
            font-size: 14px;
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
        
        .btn-submit {
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
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .btn-back {
            background: #f5f5f5;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 16px;
            color: #666;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
            color: #333;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-to-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <!-- Header -->
            <div class="forgot-password-header">
                <i class="fas fa-key"></i>
                <h2>Quên Mật Khẩu</h2>
                <p>Nhập email để nhận hướng dẫn reset mật khẩu</p>
            </div>
            
            <!-- Body -->
            <div class="forgot-password-body">
                
                <!-- Info Box -->
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        <strong>Lưu ý:</strong> Vui lòng nhập email đã đăng ký tài khoản. 
                        Hệ thống sẽ gửi hướng dẫn reset mật khẩu đến email của bạn.
                    </p>
                </div>
                
                <!-- Error Messages -->
                <?php if (isset($errors['email']) && !empty($errors['email'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php foreach ($errors['email'] as $error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Forgot Password Form -->
                <form method="POST" action="<?= base_url('auth/forgot-password') ?>" id="forgotPasswordForm" novalidate>
                    
                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Địa chỉ Email
                        </label>
                        <input 
                            type="email" 
                            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                            id="email" 
                            name="email"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                            placeholder="Nhập email đã đăng ký"
                            required
                            autofocus
                        >
                        <small class="text-muted">
                            <i class="fas fa-shield-alt"></i> 
                            Chúng tôi sẽ không chia sẻ email của bạn với bất kỳ ai
                        </small>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-paper-plane"></i> Gửi Yêu Cầu
                    </button>
                    
                    <!-- Back Button -->
                    <a href="<?= base_url('auth/login') ?>" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Quay Lại Đăng Nhập
                    </a>
                </form>
                
                <!-- Additional Links -->
                <div class="back-to-login">
                    <p class="mb-2">
                        <i class="fas fa-question-circle"></i> Bạn cần hỗ trợ?
                    </p>
                    <p class="mb-0">
                        Liên hệ: 
                        <a href="mailto:admin@beautyshop.com">
                            <i class="fas fa-envelope"></i> admin@beautyshop.com
                        </a>
                    </p>
                    <p class="mt-2 mb-0">
                        Hoặc gọi: 
                        <a href="tel:1900xxxx">
                            <i class="fas fa-phone"></i> 1900-xxxx
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
        // Form Validation
        const form = document.getElementById('forgotPasswordForm');
        const emailInput = document.getElementById('email');
        
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Additional email validation
            const emailValue = emailInput.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailPattern.test(emailValue)) {
                event.preventDefault();
                emailInput.classList.add('is-invalid');
                
                // Show error message
                let errorDiv = emailInput.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.style.marginTop = '5px';
                    emailInput.parentNode.appendChild(errorDiv);
                }
                errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Email không hợp lệ';
            }
            
            form.classList.add('was-validated');
        });
        
        // Remove invalid class on input
        emailInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const errorDiv = this.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        });
    </script>
</body>
</html>


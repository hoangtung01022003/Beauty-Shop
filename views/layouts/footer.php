<!-- Footer -->
<footer class="bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row">
            <!-- About -->
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-star-fill text-primary"></i> Cosmetic Shop
                </h5>
                <p class="text-white-50">
                    Cửa hàng mỹ phẩm chính hãng, uy tín hàng đầu Việt Nam. 
                    Chúng tôi cam kết mang đến cho bạn những sản phẩm chất lượng nhất.
                </p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-md-2 mb-4">
                <h6 class="fw-bold mb-3">Liên kết</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url() ?>" class="text-white-50 text-decoration-none">Trang chủ</a></li>
                    <li><a href="<?= base_url('products') ?>" class="text-white-50 text-decoration-none">Sản phẩm</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Giới thiệu</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Customer Support -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">Hỗ trợ khách hàng</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Chính sách bảo mật</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Hướng dẫn mua hàng</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Câu hỏi thường gặp</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">Liên hệ</h6>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt"></i>
                        123 Đường ABC, Quận XYZ, TP.HCM
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone"></i>
                        0123-456-789
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope"></i>
                        contact@cosmetic.vn
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <small class="text-white-50">
                    &copy; <?= date('Y') ?> Cosmetic Shop. All rights reserved.
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-white-50">
                    Thiết kế bởi <a href="#" class="text-primary text-decoration-none">WebDev Team</a>
                </small>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= base_url('public/js/cart.js') ?>"></script>
<script src="<?= base_url('public/js/main.js') ?>"></script>

</body>
</html>


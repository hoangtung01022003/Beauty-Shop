-- =====================================================
-- WEBSITE BÁN MỸ PHẨM - DỮ LIỆU MẪU (SEED DATA)
-- Ngày tạo: 11/11/2025
-- Phiên bản: 1.0.0
-- =====================================================

USE beauty_shop;

-- =====================================================
-- 1. DỮ LIỆU USERS
-- =====================================================
-- Mật khẩu mặc định: "123456" (đã mã hóa bằng password_hash)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (username, password, email, role, phone, address) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@beautyshop.com', 'admin', '0901234567', 'Hà Nội, Việt Nam'),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user1@gmail.com', 'user', '0912345678', 'Hồ Chí Minh, Việt Nam'),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user2@gmail.com', 'user', '0923456789', 'Đà Nẵng, Việt Nam'),
('nguyenvana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nguyenvana@gmail.com', 'user', '0934567890', 'Hải Phòng, Việt Nam'),
('tranthib', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tranthib@gmail.com', 'user', '0945678901', 'Cần Thơ, Việt Nam');

-- =====================================================
-- 2. DỮ LIỆU CATEGORIES
-- =====================================================
INSERT INTO categories (name, description, image) VALUES
('Chăm Sóc Da Mặt', 'Các sản phẩm dưỡng da, làm sạch, serum, kem chống nắng', 'skincare.jpg'),
('Trang Điểm', 'Son môi, phấn nền, mascara, phấn mắt', 'makeup.jpg'),
('Chăm Sóc Cơ Thể', 'Sữa tắm, kem dưỡng thể, tẩy tế bào chết', 'bodycare.jpg'),
('Chăm Sóc Tóc', 'Dầu gội, dầu xả, kem ủ tóc, tinh dầu dưỡng tóc', 'haircare.jpg'),
('Nước Hoa', 'Nước hoa nam, nữ, unisex các thương hiệu', 'perfume.jpg'),
('Mặt Nạ', 'Mặt nạ giấy, mặt nạ ngủ, mặt nạ đất sét', 'mask.jpg');

-- =====================================================
-- 3. DỮ LIỆU PRODUCTS
-- =====================================================
INSERT INTO products (name, description, price, cost_price, image, category_id, stock, sold, rating, status) VALUES
-- Chăm Sóc Da Mặt
('Kem Chống Nắng Anessa SPF50+', 'Kem chống nắng bảo vệ hoàn hảo cho da. Chống tia UV, chống nước, chống mồ hôi. Thành phần lành tính, phù hợp mọi loại da.', 450000, 350000, 'anessa-spf50.jpg', 1, 100, 45, 4.8, 'active'),
('Serum Vitamin C The Ordinary', 'Serum Vitamin C nguyên chất 23% + HA 2%. Giúp làm sáng da, mờ thâm nám, chống oxy hóa mạnh mẽ.', 320000, 250000, 'serum-vitamin-c.jpg', 1, 80, 67, 4.7, 'active'),
('Sữa Rửa Mặt Cetaphil Gentle', 'Sữa rửa mặt dịu nhẹ dành cho da nhạy cảm. Làm sạch sâu mà không gây khô da. pH cân bằng.', 280000, 220000, 'cetaphil-cleanser.jpg', 1, 150, 89, 4.9, 'active'),
('Kem Dưỡng Ẩm La Roche-Posay Toleriane', 'Kem dưỡng ẩm chuyên sâu cho da nhạy cảm, kích ứng. Làm dịu da tức thì, phục hồi hàng rào bảo vệ da.', 520000, 420000, 'laroche-toleriane.jpg', 1, 60, 34, 4.6, 'active'),
('Toner Some By Mi AHA BHA PHA', 'Toner làm sạch sâu lỗ chân lông, kiểm soát dầu. Kết hợp 3 loại acid nhẹ nhàng, phù hợp da mụn.', 380000, 300000, 'somebymi-toner.jpg', 1, 90, 56, 4.5, 'active'),

-- Trang Điểm
('Son Kem Lì 3CE Velvet Lip Tint', 'Son kem lì lâu trôi, màu sắc chuẩn Hàn Quốc. Không gây khô môi, chứa vitamin E dưỡng ẩm.', 320000, 250000, '3ce-velvet-tint.jpg', 2, 120, 98, 4.8, 'active'),
('Phấn Nền Cushion Laneige BB', 'Phấn nước che phủ hoàn hảo, tự nhiên. SPF50+ PA+++. Dưỡng ẩm 24h, kiềm dầu tốt.', 680000, 550000, 'laneige-cushion.jpg', 2, 70, 45, 4.7, 'active'),
('Mascara Maybelline Lash Sensational', 'Mascara làm dài mi, cong vút tự nhiên. Không lem, không vón cục. Waterproof.', 250000, 180000, 'maybelline-mascara.jpg', 2, 100, 72, 4.6, 'active'),
('Phấn Mắt Etude House Play Color', 'Bảng phấn mắt 10 ô màu, lên màu chuẩn. Kết cấu mịn, dễ tán, bám lâu.', 420000, 320000, 'etude-eyeshadow.jpg', 2, 50, 28, 4.5, 'active'),

-- Chăm Sóc Cơ Thể
('Sữa Tắm Dove Dưỡng Ẩm Sâu', 'Sữa tắm dưỡng ẩm với 1/4 kem dưỡng ẩm. Làm mềm mượt da, hương thơm nhẹ nhàng.', 180000, 130000, 'dove-bodywash.jpg', 3, 200, 156, 4.7, 'active'),
('Kem Dưỡng Thể Vaseline Healthy White', 'Kem dưỡng thể dưỡng trắng da toàn thân. Vitamin B3, SPF24 PA++. Thẩm thấu nhanh.', 220000, 170000, 'vaseline-lotion.jpg', 3, 150, 89, 4.6, 'active'),
('Tẩy Tế Bào Chết St.Ives Apricot', 'Tẩy da chết cho cơ thể với hạt mơ tự nhiên. Làm sạch sâu, làm mịn da, thơm mát.', 290000, 230000, 'stives-scrub.jpg', 3, 80, 54, 4.8, 'active'),

-- Chăm Sóc Tóc
('Dầu Gội Tresemme Keratin Smooth', 'Dầu gội phục hồi tóc hư tổn với keratin. Làm mềm mượt, giảm xơ rối, suôn thẳng.', 320000, 250000, 'tresemme-shampoo.jpg', 4, 100, 67, 4.5, 'active'),
('Kem Ủ Tóc Hask Argan Oil', 'Kem ủ tóc phục hồi chuyên sâu với dầu argan. Nuôi dưỡng tóc từ gốc đến ngọn, phục hồi tóc hư tổn.', 380000, 300000, 'hask-treatment.jpg', 4, 60, 34, 4.7, 'active'),
('Tinh Dầu Dưỡng Tóc Moroccanoil', 'Tinh dầu dưỡng tóc cao cấp từ Morocco. Làm bóng mượt, giảm xơ rối, bảo vệ tóc khỏi nhiệt.', 850000, 700000, 'moroccanoil.jpg', 4, 40, 23, 4.9, 'active'),

-- Nước Hoa
('Nước Hoa Chanel No.5 EDP 100ml', 'Nước hoa nữ kinh điển, sang trọng. Hương hoa cỏ quyến rũ, lưu hương cả ngày.', 3200000, 2800000, 'chanel-no5.jpg', 5, 30, 12, 5.0, 'active'),
('Nước Hoa Dior Sauvage EDT 100ml', 'Nước hoa nam mạnh mẽ, nam tính. Hương gỗ cay nồng, phù hợp mọi dịp.', 2800000, 2400000, 'dior-sauvage.jpg', 5, 25, 15, 4.9, 'active'),
('Nước Hoa Jo Malone Wood Sage 100ml', 'Nước hoa unisex thanh lịch. Hương gỗ xô thơm nhẹ nhàng, tinh tế.', 3500000, 3000000, 'jomalone-woodsage.jpg', 5, 20, 8, 4.8, 'active'),

-- Mặt Nạ
('Mặt Nạ Innisfree My Real Squeeze', 'Mặt nạ giấy chiết xuất thiên nhiên. Cung cấp dưỡng chất, làm sáng da, cấp ẩm tức thì.', 25000, 18000, 'innisfree-mask.jpg', 6, 500, 234, 4.6, 'active'),
('Mặt Nạ Ngủ Laneige Water Sleeping Mask', 'Mặt nạ ngủ cấp ẩm chuyên sâu. Da mềm mượt, căng bóng vào sáng hôm sau.', 580000, 480000, 'laneige-sleeping.jpg', 6, 80, 56, 4.8, 'active'),
('Mặt Nạ Đất Sét Innisfree Jeju Volcanic', 'Mặt nạ đất sét núi lửa Jeju. Hút sạch bã nhờn, se khít lỗ chân lông, sạch mụn.', 320000, 250000, 'innisfree-volcanic.jpg', 6, 90, 67, 4.7, 'active');

-- =====================================================
-- 4. DỮ LIỆU ORDERS
-- =====================================================
INSERT INTO orders (user_id, order_code, total_price, discount, final_price, status, payment_method, shipping_address, notes) VALUES
(2, 'ORD-2025-001', 770000, 50000, 720000, 'delivered', 'cash', 'Quận 1, TP. Hồ Chí Minh', 'Giao giờ hành chính'),
(3, 'ORD-2025-002', 1000000, 0, 1000000, 'shipped', 'bank', 'Quận Hải Châu, Đà Nẵng', 'Gọi trước khi giao'),
(4, 'ORD-2025-003', 2800000, 200000, 2600000, 'confirmed', 'momo', 'Quận Lê Chân, Hải Phòng', NULL),
(2, 'ORD-2025-004', 560000, 60000, 500000, 'pending', 'cash', 'Quận 3, TP. Hồ Chí Minh', 'Giao cuối tuần'),
(5, 'ORD-2025-005', 3200000, 0, 3200000, 'delivered', 'bank', 'Quận Ninh Kiều, Cần Thơ', 'Khách VIP');

-- =====================================================
-- 5. DỮ LIỆU ORDER_ITEMS
-- =====================================================
INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES
-- Đơn hàng 1
(1, 1, 1, 450000, 450000),
(1, 6, 1, 320000, 320000),

-- Đơn hàng 2
(2, 7, 1, 680000, 680000),
(2, 6, 1, 320000, 320000),

-- Đơn hàng 3
(3, 16, 1, 2800000, 2800000),

-- Đơn hàng 4
(4, 3, 2, 280000, 560000),

-- Đơn hàng 5
(5, 15, 1, 3200000, 3200000);

-- =====================================================
-- KẾT THÚC SEED DATA
-- =====================================================

-- Hiển thị thống kê
SELECT 'Database seed completed!' as status;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_categories FROM categories;
SELECT COUNT(*) as total_products FROM products;
SELECT COUNT(*) as total_orders FROM orders;
SELECT COUNT(*) as total_order_items FROM order_items;


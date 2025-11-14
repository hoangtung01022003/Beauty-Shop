-- =====================================================
-- FIX CATEGORIES TABLE - Thêm field status
-- =====================================================

USE beauty_shop;

-- Kiểm tra và thêm column status nếu chưa có
ALTER TABLE categories 
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Trạng thái danh mục' 
AFTER description;

-- Cập nhật các record hiện có nếu status là NULL
UPDATE categories SET status = 'active' WHERE status IS NULL;

-- Hiển thị cấu trúc bảng để xác nhận
DESCRIBE categories;
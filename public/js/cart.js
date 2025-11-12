/**
 * =====================================================
 * CART.JS - JavaScript cho giỏ hàng
 * =====================================================
 * File: public/js/cart.js
 * Mô tả: Xử lý AJAX cho giỏ hàng
 * =====================================================
 */

// Lấy base URL từ meta tag hoặc từ window
const getBaseUrl = () => {
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    if (baseUrlMeta) {
        return baseUrlMeta.getAttribute('content');
    }
    // Fallback: lấy từ window location
    return window.location.origin + '/WebBanMyPham/';
};

const Cart = {
    /**
     * Thêm sản phẩm vào giỏ (từ trang sản phẩm)
     */
    add: function(productId, quantity = 1, callback) {
        const baseUrl = getBaseUrl();
        
        fetch(baseUrl + 'cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `product_id=${productId}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateBadge(data.cart_count);
                this.showNotification(data.message, 'success');
                
                if (callback) callback(data);
            } else {
                this.showNotification(data.message, 'error');
                if (callback) callback(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
            if (callback) callback({ success: false });
        });
    },

    /**
     * Cập nhật badge số lượng giỏ hàng
     */
    updateBadge: function(count) {
        // Tìm tất cả các badge giỏ hàng
        const badges = document.querySelectorAll('.cart-count, .cart-badge, .badge-cart');
        
        badges.forEach(badge => {
            badge.textContent = count;
            
            // Animation nhấp nháy
            badge.classList.add('animate-pulse');
            setTimeout(() => badge.classList.remove('animate-pulse'), 500);
        });
    },

    /**
     * Hiển thị thông báo
     */
    showNotification: function(message, type = 'success') {
        // Tạo alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove sau 3 giây
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 3000);
    },

    /**
     * Lấy số lượng items trong giỏ
     */
    getCount: function(callback) {
        const baseUrl = getBaseUrl();
        
        fetch(baseUrl + 'cart/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && callback) {
                callback(data);
            }
        })
        .catch(error => console.error('Error:', error));
    }
};

// Event: Add to cart từ nút "Thêm vào giỏ"
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart buttons (từ product cards)
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const quantity = this.dataset.quantity || 1;
            const originalHtml = this.innerHTML;
            
            // Disable button và hiển thị loading
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';
            
            Cart.add(productId, quantity, (data) => {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = originalHtml;
            });
        });
    });

    // Quick add to cart (số lượng = 1)
    document.querySelectorAll('.quick-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            Cart.add(productId, 1);
        });
    });
});


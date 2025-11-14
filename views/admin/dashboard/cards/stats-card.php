<?php
/**
 * =====================================================
 * STATS CARD COMPONENT - Component card thống kê
 * =====================================================
 * File: views/admin/dashboard/cards/stats-card.php
 * Mô tả: Component tái sử dụng cho các card thống kê
 * =====================================================
 */

/**
 * Render stats card
 * @param array $options - ['title', 'value', 'icon', 'color', 'trend', 'trendDirection']
 */
function renderStatsCard($options) {
    $title = $options['title'] ?? 'Title';
    $value = $options['value'] ?? '0';
    $icon = $options['icon'] ?? 'fa-chart-line';
    $color = $options['color'] ?? 'primary'; // primary, success, warning, danger
    $trend = $options['trend'] ?? null;
    $trendDirection = $options['trendDirection'] ?? 'up'; // up, down
    $link = $options['link'] ?? null;
    
    ?>
    <div class="stats-card <?= $color ?>">
        <div class="icon">
            <i class="fas <?= $icon ?>"></i>
        </div>
        <h3><?= $value ?></h3>
        <p><?= htmlspecialchars($title) ?></p>
        
        <?php if ($trend): ?>
            <div class="trend <?= $trendDirection ?>">
                <i class="fas fa-arrow-<?= $trendDirection === 'up' ? 'up' : 'down' ?>"></i>
                <?= $trend ?> so với tháng trước
            </div>
        <?php endif; ?>
        
        <?php if ($link): ?>
            <a href="<?= $link ?>" class="stretched-link"></a>
        <?php endif; ?>
    </div>
    <?php
}
?>


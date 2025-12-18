<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load base path configuration
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}

// Determine base path for assets based on the calling script
// Check if the script calling this header is in pages/ directory
$callingScript = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
$isInPages = strpos($callingScript, '/pages/') !== false || strpos($callingScript, '\\pages\\') !== false;
$basePath = $isInPages ? '../' : '';

// Make $basePath available globally
if (!isset($GLOBALS['basePath'])) {
    $GLOBALS['basePath'] = $basePath;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $pageTitle ?? '123 English - Hệ Thống Đánh Giá Học Viên'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $basePath; ?>index.php">
                <img src="<?php echo $basePath; ?>assets/images/1.jpg" alt="123 English" height="50" class="me-2" onerror="this.style.display='none'">
                <span class="fw-bold text-primary">123 English</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>index.php">
                            <i class="fas fa-plus-circle"></i> Tạo Đánh Giá Mới
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>pages/list.php">
                            <i class="fas fa-list"></i> Danh Sách Đánh Giá
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>pages/reports.php">
                            <i class="fas fa-chart-bar"></i> Báo Cáo Tháng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>pages/manage_data.php">
                            <i class="fas fa-cog"></i> Quản Lý Dữ Liệu
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="main-content">


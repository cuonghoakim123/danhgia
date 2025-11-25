<?php
/**
 * Generate PDF Page
 * Kyna English Evaluation System
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

// Get evaluation ID
$evaluationId = $_GET['id'] ?? 0;

if (!$evaluationId) {
    die('Invalid evaluation ID');
}

// Check if TCPDF is installed - try multiple paths
$tcpdfPaths = [
    __DIR__ . '/../vendor/tcpdf/tcpdf.php',
    __DIR__ . '/../../vendor/tcpdf/tcpdf.php',
    dirname(__DIR__) . '/vendor/tcpdf/tcpdf.php'
];

$tcpdfFound = false;
foreach ($tcpdfPaths as $path) {
    if (file_exists($path)) {
        $tcpdfFound = true;
        break;
    }
}

if (!$tcpdfFound) {
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cài đặt TCPDF</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <div class="container py-5">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Thư viện TCPDF chưa được cài đặt</h4>
                </div>
                <div class="card-body">
                    <p class="lead">Vui lòng cài đặt TCPDF để sử dụng tính năng xuất PDF:</p>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-download"></i> Cách 1: Sử dụng Composer (Khuyến nghị)</h5>
                        <p>Mở Command Prompt hoặc Terminal và chạy:</p>
                        <pre class="bg-dark text-white p-3 rounded"><code>cd C:\xampp\htdocs\webstieenghlish
composer require tecnickcom/tcpdf</code></pre>
                        <p class="mb-0"><small>Lưu ý: Cần cài đặt Composer trước. Tải tại: <a href="https://getcomposer.org/download/" target="_blank">getcomposer.org</a></small></p>
                    </div>
                    
                    <div class="alert alert-secondary">
                        <h5><i class="fas fa-file-archive"></i> Cách 2: Tải thủ công</h5>
                        <ol>
                            <li>Tải TCPDF từ: <a href="https://github.com/tecnickcom/TCPDF/archive/refs/heads/main.zip" target="_blank" class="btn btn-sm btn-primary"><i class="fab fa-github"></i> GitHub</a></li>
                            <li>Giải nén file ZIP vừa tải</li>
                            <li>Đổi tên thư mục <code>TCPDF-main</code> thành <code>tcpdf</code></li>
                            <li>Copy thư mục <code>tcpdf</code> vào: <code>C:\xampp\htdocs\webstieenghlish\vendor\</code></li>
                            <li>Kết quả: <code>C:\xampp\htdocs\webstieenghlish\vendor\tcpdf\tcpdf.php</code> phải tồn tại</li>
                        </ol>
                    </div>
                    
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle"></i> Sau khi cài đặt</h5>
                        <p class="mb-0">Refresh trang này hoặc quay lại và thử xuất PDF lại.</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="preview.php?id=<?php echo $evaluationId; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <a href="../index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Về Trang Chủ
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    // Include PDF generator
    require_once '../vendor/pdf_generator.php';
    
    // Generate PDF
    $result = generateEvaluationPDF($evaluationId);
    
    // Output PDF to browser
    $pdfPath = $result['file_path'];
    
    if (file_exists($pdfPath)) {
        // Get evaluation data for better filename
        $evaluation = getEvaluationById($evaluationId);
        $displayName = $evaluation ? $evaluation['full_name'] : 'DanhGia';
        
        // Clean filename for download
        require_once '../includes/vietnamese_helper.php';
        $cleanName = removeVietnameseAccents($displayName);
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '_', $cleanName);
        $cleanName = preg_replace('/_+/', '_', $cleanName);
        $cleanName = trim($cleanName, '_');
        $cleanName = substr($cleanName, 0, 30);
        
        $downloadName = 'KetQuaDanhGia_' . $cleanName . '_' . date('Y-m-d') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($pdfPath));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        readfile($pdfPath);
        exit;
    } else {
        throw new Exception('Không thể tạo file PDF');
    }
    
} catch (Exception $e) {
    error_log("PDF Generation Error: " . $e->getMessage());
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lỗi tạo PDF</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container py-5">
            <div class="alert alert-danger">
                <h4><i class="fas fa-exclamation-circle"></i> Lỗi tạo PDF</h4>
                <p><?php echo htmlspecialchars($e->getMessage()); ?></p>
            </div>
            <a href="preview.php?id=<?php echo $evaluationId; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </body>
    </html>
    <?php
}


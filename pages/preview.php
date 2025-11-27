<?php
/**
 * Preview Evaluation Before PDF Generation
 * Kyna English Evaluation System
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Xem Trước Đánh Giá - Kyna English';

// Get evaluation ID
$evaluationId = $_GET['id'] ?? 0;

if (!$evaluationId) {
    header('Location: ../index.php');
    exit;
}

// Get evaluation data
$evaluation = getEvaluationById($evaluationId);

if (!$evaluation) {
    header('Location: ../index.php');
    exit;
}

// Get learning paths
$learningPaths = getLearningPathsByEvaluationId($evaluationId);

// Parse strengths and improvements
$strengths = array_filter(explode("\n• ", $evaluation['strengths']));
$improvements = array_filter(explode("\n• ", $evaluation['improvements']));

ob_start();
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-eye"></i> XEM TRƯỚC ĐÁNH GIÁ</h1>
        <p>Kiểm tra thông tin trước khi xuất PDF</p>
    </div>
    
    <!-- Action Buttons -->
    <div class="card fade-in">
        <div class="card-body text-center">
            <a href="generate_pdf.php?id=<?php echo $evaluationId; ?>" 
               class="btn btn-success btn-lg"
               target="_blank">
                <i class="fas fa-file-pdf"></i> Xuất PDF
            </a>
            <a href="edit.php?id=<?php echo $evaluationId; ?>" 
               class="btn btn-warning btn-lg">
                <i class="fas fa-edit"></i> Chỉnh Sửa
            </a>
            <a href="list.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-list"></i> Danh Sách Đánh Giá
            </a>
            <a href="../index.php" class="btn btn-info btn-lg">
                <i class="fas fa-plus"></i> Tạo Mới
            </a>
        </div>
    </div>
    
    <!-- Preview Content -->
    <div id="previewContent">
        
        <!-- Evaluation Header -->
        <div class="card fade-in">
            <div class="card-body text-center">
                <img src="../assets/images/logo.svg" alt="Kyna English" height="60" class="mb-3" onerror="this.style.display='none'">
                <h2 class="text-primary fw-bold mb-0">KẾT QUẢ ĐÁNH GIÁ</h2>
            </div>
        </div>
        
        <!-- Student Information -->
        <div class="card fade-in">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="pdf-info-item">
                            <div class="pdf-info-label">Họ và tên Học viên</div>
                            <div class="pdf-info-value"><?php echo htmlspecialchars($evaluation['full_name']); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pdf-info-item">
                            <div class="pdf-info-label">Loại học viên</div>
                            <div class="pdf-info-value"><?php echo htmlspecialchars($evaluation['student_type']); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pdf-info-item">
                            <div class="pdf-info-label">Mã báo danh</div>
                            <div class="pdf-info-value"><?php echo htmlspecialchars($evaluation['student_code']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Strengths Section -->
        <div class="card fade-in">
            <div class="card-header" style="background: linear-gradient(135deg, #52c166, #45a858);">
                <i class="fas fa-thumbs-up"></i> CÁC ĐIỂM TỐT:
            </div>
            <div class="card-body">
                <ul class="pdf-list">
                    <?php foreach ($strengths as $strength): ?>
                        <?php if (trim($strength)): ?>
                            <li>👉 <?php echo htmlspecialchars($strength); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($evaluation['strengths_evaluation'])): ?>
                    <div class="mt-3 p-3" style="background-color: #f8f9fa; border-left: 4px solid #52c166; border-radius: 4px;">
                        <strong>Đánh giá giác:</strong>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($evaluation['strengths_evaluation'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Improvements Section -->
        <div class="card fade-in">
            <div class="card-header" style="background: linear-gradient(135deg, #ff9800, #f57c00);">
                <i class="fas fa-arrow-up"></i> CÁC ĐIỂM CẦN CẢI THIỆN:
            </div>
            <div class="card-body">
                <ul class="pdf-list">
                    <?php foreach ($improvements as $improvement): ?>
                        <?php if (trim($improvement)): ?>
                            <li>👉 <?php echo htmlspecialchars($improvement); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($evaluation['improvements_evaluation'])): ?>
                    <div class="mt-3 p-3" style="background-color: #f8f9fa; border-left: 4px solid #ff9800; border-radius: 4px;">
                        <strong>Đánh giá giác:</strong>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($evaluation['improvements_evaluation'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Summary Section -->
        <div class="card fade-in">
            <div class="card-body">
                <div class="pdf-summary-box">
                    <h3><i class="fas fa-clipboard-check"></i> TỔNG KẾT:</h3>
                    <div class="mb-3">
                        <strong>Chương trình</strong>
                        <div class="program-badge">
                            <?php echo htmlspecialchars($evaluation['program_name']); ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Level:</strong> <?php echo htmlspecialchars($evaluation['course_level']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày đánh giá:</strong> <?php echo formatDate($evaluation['evaluation_date']); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($evaluation['summary']): ?>
                        <div class="mt-3">
                            <strong>Ghi chú:</strong>
                            <p><?php echo nl2br(htmlspecialchars($evaluation['summary'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Learning Path Section -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-route"></i> LỘ TRÌNH:
            </div>
            <div class="card-body">
                <?php if ($learningPaths): ?>
                    <table class="table table-bordered pdf-learning-path-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Khóa</th>
                                <th style="width: 15%;">Số buổi</th>
                                <th style="width: 60%;">Kết quả học tập</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($learningPaths as $path): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($path['course_name']); ?></strong></td>
                                    <td class="text-center"><?php echo $path['lessons_count']; ?></td>
                                    <td>
                                        <div><strong>Với lộ trình này, học viên sẽ học <?php echo $path['lessons_count']; ?> buổi và đạt được</strong></div>
                                        <div class="mt-2"><?php echo nl2br(htmlspecialchars($path['learning_outcomes'])); ?></div>
                                        <?php if ($path['topics']): ?>
                                            <div class="mt-2">
                                                <strong>👉 Các chủ đề giao tiếp:</strong><br>
                                                <?php echo htmlspecialchars($path['topics']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mt-2">
                                            <strong>👉 Học tập trung vào phần xa Nghe-Nói, giao tiếp các tình huống trong cuộc sống hàng ngày</strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Chưa có lộ trình học nào</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="card fade-in">
            <div class="card-body text-center pdf-footer">
                <img src="../assets/images/logo.svg" alt="Kyna English" height="40" class="mb-3" onerror="this.style.display='none'">
                <div class="pdf-contact">
                    <div class="pdf-contact-item">
                        <i class="fas fa-phone pdf-contact-icon"></i>
                        <strong>1900 6364 09</strong>
                    </div>
                    <div class="pdf-contact-item">
                        <i class="fas fa-envelope pdf-contact-icon"></i>
                        <strong>hotro@kynaforkids.vn</strong>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</div>

<?php
$content = ob_get_clean();
$extraCSS = '<link rel="stylesheet" href="../assets/css/print.css">';
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>


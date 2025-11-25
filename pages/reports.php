<?php
/**
 * Monthly Reports - Báo Cáo Theo Tháng
 * Kyna English Evaluation System
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Báo Cáo Theo Tháng - Kyna English';

// Get month and year from request
$selectedMonth = $_GET['month'] ?? date('m');
$selectedYear = $_GET['year'] ?? date('Y');

// Validate
$selectedMonth = (int)$selectedMonth;
$selectedYear = (int)$selectedYear;

if ($selectedMonth < 1 || $selectedMonth > 12) {
    $selectedMonth = (int)date('m');
}
if ($selectedYear < 2020 || $selectedYear > 2100) {
    $selectedYear = (int)date('Y');
}

// Get data
$evaluations = getEvaluationsByMonth($selectedMonth, $selectedYear);
$statistics = getMonthlyStatistics($selectedMonth, $selectedYear);
$availableMonths = getAvailableMonths();

// Month names in Vietnamese
$monthNames = [
    1 => 'Tháng Một', 2 => 'Tháng Hai', 3 => 'Tháng Ba', 4 => 'Tháng Tư',
    5 => 'Tháng Năm', 6 => 'Tháng Sáu', 7 => 'Tháng Bảy', 8 => 'Tháng Tám',
    9 => 'Tháng Chín', 10 => 'Tháng Mười', 11 => 'Tháng Mười Một', 12 => 'Tháng Mười Hai'
];

$currentMonthName = $monthNames[$selectedMonth] ?? '';

ob_start();
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-chart-bar"></i> BÁO CÁO THEO THÁNG</h1>
        <p>Xem toàn bộ đánh giá và thống kê trong tháng</p>
    </div>
    
    <!-- Filter Section -->
    <div class="card fade-in mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> LỌC BÁO CÁO
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tháng</label>
                    <select name="month" class="form-select" required>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $selectedMonth == $m ? 'selected' : ''; ?>>
                                <?php echo $monthNames[$m]; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Năm</label>
                    <select name="year" class="form-select" required>
                        <?php 
                        $currentYear = (int)date('Y');
                        for ($y = $currentYear; $y >= 2020; $y--): 
                        ?>
                            <option value="<?php echo $y; ?>" <?php echo $selectedYear == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Xem Báo Cáo
                    </button>
                </div>
            </form>
            
            <!-- Quick Links -->
            <?php if ($availableMonths): ?>
                <div class="mt-3">
                    <small class="text-muted">Tháng có dữ liệu: </small>
                    <?php foreach (array_slice($availableMonths, 0, 6) as $month): ?>
                        <a href="?month=<?php echo $month['month']; ?>&year=<?php echo $month['year']; ?>" 
                           class="badge bg-secondary text-decoration-none me-1">
                            <?php echo $month['display']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h3 class="text-primary mb-0"><?php echo $statistics['total_evaluations']; ?></h3>
                    <p class="text-muted mb-0">Tổng Đánh Giá</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h3 class="text-success mb-0"><?php echo $statistics['total_students']; ?></h3>
                    <p class="text-muted mb-0">Học Viên</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h3 class="text-info mb-0"><?php echo count($statistics['by_course']); ?></h3>
                    <p class="text-muted mb-0">Khóa Học</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h3 class="text-warning mb-0"><?php echo count($statistics['by_day']); ?></h3>
                    <p class="text-muted mb-0">Ngày Có Đánh Giá</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Details -->
    <div class="row mb-4">
        <!-- By Student Type -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users"></i> Theo Loại Học Viên
                </div>
                <div class="card-body">
                    <?php if ($statistics['by_type']): ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Loại</th>
                                    <th class="text-end">Số Lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statistics['by_type'] as $type): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($type['student_type']); ?></td>
                                        <td class="text-end">
                                            <span class="badge bg-primary"><?php echo $type['count']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center">Chưa có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- By Course -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book"></i> Theo Khóa Học
                </div>
                <div class="card-body">
                    <?php if ($statistics['by_course']): ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Khóa Học</th>
                                    <th class="text-end">Số Lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statistics['by_course'] as $course): ?>
                                    <tr>
                                        <td>
                                            <small><?php echo htmlspecialchars($course['course_name']); ?></small><br>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($course['course_level']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-success"><?php echo $course['count']; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center">Chưa có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Evaluations List -->
    <div class="card fade-in">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-list"></i> DANH SÁCH ĐÁNH GIÁ - <?php echo strtoupper($currentMonthName . ' ' . $selectedYear); ?>
            </span>
            <div>
                <button onclick="window.print()" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-print"></i> In Báo Cáo
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if ($evaluations && count($evaluations) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="80">Ngày</th>
                                <th width="100">Mã</th>
                                <th>Học Viên</th>
                                <th width="100">Loại</th>
                                <th>Khóa Học</th>
                                <th width="100">Level</th>
                                <th width="120">Giáo Viên</th>
                                <th width="150" class="text-center">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evaluations as $eval): ?>
                                <tr>
                                    <td>
                                        <small><?php echo formatDate($eval['evaluation_date'], 'd/m'); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($eval['student_code']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($eval['full_name']); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($eval['student_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($eval['course_name']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($eval['course_level']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($eval['teacher_name'] ?: 'N/A'); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="preview.php?id=<?php echo $eval['id']; ?>" 
                                               class="btn btn-info"
                                               title="Xem trước">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="generate_pdf.php?id=<?php echo $eval['id']; ?>" 
                                               class="btn btn-success"
                                               title="Xuất PDF"
                                               target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Summary Footer -->
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <strong>Tổng Đánh Giá:</strong> <?php echo count($evaluations); ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Học Viên:</strong> <?php echo $statistics['total_students']; ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Khóa Học:</strong> <?php echo count($statistics['by_course']); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Không có đánh giá nào trong <?php echo $currentMonthName . ' ' . $selectedYear; ?></h4>
                    <a href="../index.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Tạo Đánh Giá Đầu Tiên
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Daily Breakdown -->
    <?php if ($statistics['by_day']): ?>
    <div class="card fade-in mt-4">
        <div class="card-header">
            <i class="fas fa-calendar-day"></i> Phân Bổ Theo Ngày
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($statistics['by_day'] as $day): ?>
                    <div class="col-md-2 mb-2">
                        <div class="card text-center border-secondary">
                            <div class="card-body p-2">
                                <small class="text-muted d-block"><?php echo formatDate($day['eval_date'], 'd/m'); ?></small>
                                <strong class="text-primary"><?php echo $day['count']; ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<style>
@media print {
    .page-header, .card-header, .btn, nav, footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>


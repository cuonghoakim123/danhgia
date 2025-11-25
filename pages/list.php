<?php
/**
 * List All Evaluations
 * Kyna English Evaluation System
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Danh Sách Đánh Giá - Kyna English';

// Handle search
$keyword = $_GET['search'] ?? '';
$evaluations = $keyword ? searchEvaluations($keyword) : getAllEvaluations();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['confirm'])) {
    $deleteId = (int)$_GET['delete'];
    if (deleteEvaluation($deleteId)) {
        $_SESSION['message'] = 'Xóa đánh giá thành công!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Không thể xóa đánh giá!';
        $_SESSION['message_type'] = 'danger';
    }
    header('Location: list.php');
    exit;
}

ob_start();
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-list"></i> DANH SÁCH ĐÁNH GIÁ</h1>
        <p>Quản lý tất cả các đánh giá học viên</p>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <!-- Search and Actions -->
    <div class="card fade-in">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <form method="GET" class="d-flex">
                        <input type="text" 
                               class="form-control me-2" 
                               name="search" 
                               placeholder="Tìm kiếm theo tên, mã báo danh, khóa học..."
                               value="<?php echo htmlspecialchars($keyword); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm
                        </button>
                        <?php if ($keyword): ?>
                            <a href="list.php" class="btn btn-secondary ms-2">
                                <i class="fas fa-times"></i> Xóa
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-4 text-end">
                    <a href="reports.php" class="btn btn-info me-2">
                        <i class="fas fa-chart-bar"></i> Báo Cáo Tháng
                    </a>
                    <a href="../index.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tạo Đánh Giá Mới
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Evaluations Table -->
    <div class="card fade-in">
        <div class="card-body">
            <?php if ($evaluations && count($evaluations) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Học viên</th>
                                <th>Loại</th>
                                <th>Khóa học</th>
                                <th>Level</th>
                                <th>Ngày đánh giá</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evaluations as $eval): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($eval['student_code']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($eval['full_name']); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($eval['student_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($eval['course_name']); ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($eval['course_level']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($eval['evaluation_date']); ?></td>
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
                                            <a href="edit.php?id=<?php echo $eval['id']; ?>" 
                                               class="btn btn-warning"
                                               title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $eval['id']; ?>&confirm=1" 
                                               class="btn btn-danger"
                                               title="Xóa"
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">
                        <?php echo $keyword ? 'Không tìm thấy kết quả nào' : 'Chưa có đánh giá nào'; ?>
                    </h4>
                    <a href="../index.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Tạo Đánh Giá Đầu Tiên
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<?php
$content = ob_get_clean();
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>


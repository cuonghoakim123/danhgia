<?php
/**
 * Manage Data - Courses and Criteria
 * Kyna English Evaluation System
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Quản Lý Dữ Liệu - Kyna English';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_course':
            $result = query(
                "INSERT INTO courses (course_name, course_code, course_level, total_lessons, topics, description) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $_POST['course_name'],
                    $_POST['course_code'],
                    $_POST['course_level'],
                    $_POST['total_lessons'],
                    $_POST['topics'],
                    $_POST['description']
                ]
            );
            $_SESSION['message'] = $result ? 'Thêm khóa học thành công!' : 'Có lỗi xảy ra!';
            $_SESSION['message_type'] = $result ? 'success' : 'danger';
            break;
            
        case 'add_criteria':
            $result = query(
                "INSERT INTO evaluation_criteria (criteria_text, criteria_type, category, display_order) 
                 VALUES (?, ?, ?, ?)",
                [
                    $_POST['criteria_text'],
                    $_POST['criteria_type'],
                    $_POST['category'],
                    $_POST['display_order'] ?? 0
                ]
            );
            $_SESSION['message'] = $result ? 'Thêm tiêu chí thành công!' : 'Có lỗi xảy ra!';
            $_SESSION['message_type'] = $result ? 'success' : 'danger';
            break;
            
        case 'delete_course':
            $result = query("UPDATE courses SET is_active = 0 WHERE id = ?", [$_POST['course_id']]);
            $_SESSION['message'] = $result ? 'Xóa khóa học thành công!' : 'Có lỗi xảy ra!';
            $_SESSION['message_type'] = $result ? 'success' : 'danger';
            break;
            
        case 'delete_criteria':
            $result = query("UPDATE evaluation_criteria SET is_active = 0 WHERE id = ?", [$_POST['criteria_id']]);
            $_SESSION['message'] = $result ? 'Xóa tiêu chí thành công!' : 'Có lỗi xảy ra!';
            $_SESSION['message_type'] = $result ? 'success' : 'danger';
            break;
    }
    
    header('Location: manage_data.php');
    exit;
}

// Get data
$courses = getAllCourses();
$strengthsCriteria = getCriteriaByType('strengths');
$improvementsCriteria = getCriteriaByType('improvements');

ob_start();
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-cog"></i> QUẢN LÝ DỮ LIỆU</h1>
        <p>Quản lý khóa học và tiêu chí đánh giá</p>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="manageTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button">
                <i class="fas fa-book"></i> Khóa Học
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="criteria-tab" data-bs-toggle="tab" data-bs-target="#criteria" type="button">
                <i class="fas fa-list-check"></i> Tiêu Chí Đánh Giá
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="manageTabsContent">
        
        <!-- COURSES TAB -->
        <div class="tab-pane fade show active" id="courses" role="tabpanel">
            
            <!-- Add Course Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus"></i> Thêm Khóa Học Mới
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_course">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tên khóa học <span class="required">*</span></label>
                                <input type="text" class="form-control" name="course_name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mã khóa học <span class="required">*</span></label>
                                <input type="text" class="form-control" name="course_code" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cấp độ <span class="required">*</span></label>
                                <input type="text" class="form-control" name="course_level" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label">Số buổi học <span class="required">*</span></label>
                                <input type="number" class="form-control" name="total_lessons" value="32" required>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">Các chủ đề</label>
                                <input type="text" class="form-control" name="topics">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Mô tả</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save"></i> Thêm Khóa Học
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Courses List -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list"></i> Danh Sách Khóa Học
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Tên khóa học</th>
                                    <th>Cấp độ</th>
                                    <th>Số buổi</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($course['course_code']); ?></span></td>
                                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['course_level']); ?></td>
                                        <td><?php echo $course['total_lessons']; ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_course">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Xác nhận xóa?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- CRITERIA TAB -->
        <div class="tab-pane fade" id="criteria" role="tabpanel">
            
            <!-- Add Criteria Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus"></i> Thêm Tiêu Chí Đánh Giá Mới
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_criteria">
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label">Nội dung tiêu chí <span class="required">*</span></label>
                                <textarea class="form-control" name="criteria_text" rows="3" required></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Loại <span class="required">*</span></label>
                                <select class="form-select" name="criteria_type" required>
                                    <option value="strengths">Điểm tốt</option>
                                    <option value="improvements">Điểm cần cải thiện</option>
                                </select>
                                
                                <label class="form-label mt-3">Danh mục</label>
                                <input type="text" class="form-control" name="category" placeholder="Ví dụ: Vocabulary">
                                
                                <label class="form-label mt-3">Thứ tự hiển thị</label>
                                <input type="number" class="form-control" name="display_order" value="0">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save"></i> Thêm Tiêu Chí
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Strengths Criteria -->
            <div class="card mb-4">
                <div class="card-header" style="background: #52c166; color: white;">
                    <i class="fas fa-thumbs-up"></i> Tiêu Chí Điểm Tốt
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <?php foreach ($strengthsCriteria as $criteria): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($criteria['criteria_text']); ?></td>
                                        <td width="100">
                                            <span class="badge bg-secondary"><?php echo $criteria['category']; ?></span>
                                        </td>
                                        <td width="50">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_criteria">
                                                <input type="hidden" name="criteria_id" value="<?php echo $criteria['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Xác nhận xóa?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Improvements Criteria -->
            <div class="card">
                <div class="card-header" style="background: #ff9800; color: white;">
                    <i class="fas fa-arrow-up"></i> Tiêu Chí Điểm Cần Cải Thiện
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <?php foreach ($improvementsCriteria as $criteria): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($criteria['criteria_text']); ?></td>
                                        <td width="100">
                                            <span class="badge bg-secondary"><?php echo $criteria['category']; ?></span>
                                        </td>
                                        <td width="50">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_criteria">
                                                <input type="hidden" name="criteria_id" value="<?php echo $criteria['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Xác nhận xóa?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<?php
$content = ob_get_clean();
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>


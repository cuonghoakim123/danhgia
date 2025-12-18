<?php
/**
 * Main Form - Create Student Evaluation
 * 123 English Evaluation System
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Tạo Đánh Giá Học Viên - 123 English';

// Get data for form
$strengths = getCriteriaByType('strengths');
$improvements = getCriteriaByType('improvements');
$courses = getAllCourses();
$learningOutcomeTemplates = getLearningOutcomeTemplates();

ob_start();
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-file-alt"></i> TẠO ĐÁNH GIÁ HỌC VIÊN</h1>
        <p>Điền thông tin học viên và chọn các tiêu chí đánh giá</p>
    </div>
    
    <!-- Main Form -->
    <form id="evaluationForm" method="POST" class="needs-validation" novalidate>
        
        <!-- SECTION 1: Student Information -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-user"></i> THÔNG TIN HỌC VIÊN
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="full_name" class="form-label">
                            Họ và tên Học viên <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="full_name" 
                               name="full_name"
                               placeholder="Nhập họ tên học viên"
                               required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="student_type" class="form-label">
                            Loại học viên <span class="required">*</span>
                        </label>
                        <select class="form-select" id="student_type" name="student_type" required>
                            <option value="">-- Chọn loại học viên --</option>
                            <option value="Trẻ em">Trẻ em</option>
                            <option value="Thiếu niên">Thiếu niên</option>
                            <option value="Người lớn">Người lớn</option>
                        </select>
                    </div>                  
            </div>
        </div>
        
        <!-- SECTION 2: Strengths (Các điểm tốt) -->
        <div class="card fade-in" id="strengthsSection">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-thumbs-up"></i> CÁC ĐIỂM TỐT</span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-light select-all-strengths">
                        <i class="fas fa-check-double"></i> Chọn tất cả
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="criteria-section">
                    <?php if ($strengths): ?>
                        <?php foreach ($strengths as $criterion): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="strengths[]" 
                                       value="<?php echo htmlspecialchars($criterion['criteria_text']); ?>"
                                       id="strength_<?php echo $criterion['id']; ?>">
                                <label class="form-check-label" for="strength_<?php echo $criterion['id']; ?>">
                                    <?php echo htmlspecialchars($criterion['criteria_text']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Chưa có tiêu chí nào được thiết lập</p>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <label for="strengths_evaluation" class="form-label">
                        Đánh giá khác
                    </label>
                    <textarea class="form-control" 
                              id="strengths_evaluation" 
                              name="strengths_evaluation"
                              rows="3"
                              placeholder="Nhập đánh giá giác cho các điểm tốt"></textarea>
                </div>
            </div>
        </div>
        
        <!-- SECTION 3: Improvements (Các điểm cần cải thiện) -->
        <div class="card fade-in" id="improvementsSection">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-arrow-up"></i> CÁC ĐIỂM CẦN CẢI THIỆN</span>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-light select-all-improvements">
                        <i class="fas fa-check-double"></i> Chọn tất cả
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="criteria-section">
                    <?php if ($improvements): ?>
                        <?php foreach ($improvements as $criterion): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="improvements[]" 
                                       value="<?php echo htmlspecialchars($criterion['criteria_text']); ?>"
                                       id="improvement_<?php echo $criterion['id']; ?>">
                                <label class="form-check-label" for="improvement_<?php echo $criterion['id']; ?>">
                                    <?php echo htmlspecialchars($criterion['criteria_text']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Chưa có tiêu chí nào được thiết lập</p>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <label for="improvements_evaluation" class="form-label">
                        Đánh giá khác
                    </label>
                    <textarea class="form-control" 
                              id="improvements_evaluation" 
                              name="improvements_evaluation"
                              rows="3"
                              placeholder="Nhập đánh giá giác cho các điểm cần cải thiện"></textarea>
                </div>
            </div>
        </div>
        
        <!-- SECTION 4: Program Selection (Chương trình học) -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-book"></i> CHƯƠNG TRÌNH HỌC
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label">
                            Chọn khóa học <span class="required">*</span>
                        </label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">-- Chọn khóa học --</option>
                            <?php if ($courses): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($course['course_name']); ?> 
                                        (<?php echo $course['total_lessons']; ?> buổi)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="program_name" class="form-label">
                            Tên chương trình <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="program_name" 
                               name="program_name"
                               placeholder="Ví dụ: Daily English - Cấp độ DE Beginner 1"
                               required>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="evaluation_date" class="form-label">
                            Ngày đánh giá <span class="required">*</span>
                        </label>
                        <input type="date" 
                               class="form-control" 
                               id="evaluation_date" 
                               name="evaluation_date"
                               value="<?php echo date('Y-m-d'); ?>"
                               required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">
                            Thời gian học là 30 buổi
                        </label>
                        <div class="form-control" style="background-color: #e9ecef; border: 1px solid #ced4da;">
                            30 buổi
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SECTION 5: Learning Path (Lộ trình học) -->
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-route"></i> LỘ TRÌNH HỌC</span>
                <button type="button" class="btn btn-sm btn-outline-light" id="addPathBtn">
                    <i class="fas fa-plus"></i> Thêm khóa học
                </button>
            </div>
            <div class="card-body">
                <div id="learningPathsContainer">
                    <!-- First learning path row -->
                    <div class="learning-path-row" data-path-index="0">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Khóa học <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="learning_paths[0][course_name]"
                                       placeholder="Ví dụ: DE Beginner 1"
                                       required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Số buổi <span class="required">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       name="learning_paths[0][lessons_count]"
                                       min="1"
                                       value="32"
                                       required>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SECTION 6: Summary (Tổng kết - Optional) -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-clipboard-check"></i> TỔNG KẾT (TÙY CHỌN)
            </div>
            <div class="card-body">
                <label for="summary" class="form-label">
                    Ghi chú thêm
                </label>
                <textarea class="form-control" 
                          id="summary" 
                          name="summary"
                          rows="4"
                          maxlength="1000"
                          placeholder="Thêm ghi chú, nhận xét tổng quan về học viên (nếu có)"></textarea>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="card fade-in">
            <div class="card-body text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Lưu và Xem Trước
                </button>
                <a href="pages/list.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </div>
        
    </form>
</div>

<?php
$content = ob_get_clean();
// Prepare learning outcome templates for JavaScript
$templatesJson = json_encode(array_map(function($t) {
    return [
        'id' => $t['id'],
        'text' => $t['template_text']
    ];
}, $learningOutcomeTemplates));
$extraJS = '<script>
    const learningOutcomeTemplates = ' . $templatesJson . ';
</script>
<script src="assets/js/validation.js"></script>';
include 'includes/header.php';
echo $content;
include 'includes/footer.php';
?>


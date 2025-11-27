<?php
/**
 * Edit Evaluation Page
 * Kyna English Evaluation System
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'Chỉnh Sửa Đánh Giá - Kyna English';

// Get evaluation ID
$evaluationId = $_GET['id'] ?? 0;

if (!$evaluationId) {
    header('Location: list.php');
    exit;
}

// Get evaluation data
$evaluation = getEvaluationById($evaluationId);

if (!$evaluation) {
    header('Location: list.php');
    exit;
}

// Get learning paths
$learningPaths = getLearningPathsByEvaluationId($evaluationId);

// Get data for form
$strengths = getCriteriaByType('strengths');
$improvements = getCriteriaByType('improvements');
$courses = getAllCourses();
$learningOutcomeTemplates = getLearningOutcomeTemplates();

// Parse selected strengths and improvements
$selectedStrengths = array_filter(explode("\n• ", $evaluation['strengths']));
$selectedImprovements = array_filter(explode("\n• ", $evaluation['improvements']));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update student
    query(
        "UPDATE students SET full_name = ?, student_type = ? WHERE student_code = ?",
        [
            $_POST['full_name'],
            $_POST['student_type'],
            $_POST['student_code']
        ]
    );
    
    // Update evaluation
    $strengthsText = isset($_POST['strengths']) ? implode("\n• ", $_POST['strengths']) : '';
    $improvementsText = isset($_POST['improvements']) ? implode("\n• ", $_POST['improvements']) : '';
    
    query(
        "UPDATE evaluations SET 
            course_id = ?,
            program_name = ?,
            evaluation_date = ?,
            teacher_name = ?,
            strengths = ?,
            improvements = ?,
            strengths_evaluation = ?,
            improvements_evaluation = ?,
            summary = ?
        WHERE id = ?",
        [
            $_POST['course_id'],
            $_POST['program_name'],
            $_POST['evaluation_date'],
            $_POST['teacher_name'] ?? '',
            $strengthsText,
            $improvementsText,
            $_POST['strengths_evaluation'] ?? '',
            $_POST['improvements_evaluation'] ?? '',
            $_POST['summary'] ?? '',
            $evaluationId
        ]
    );
    
    // Delete old learning paths
    query("DELETE FROM learning_paths WHERE evaluation_id = ?", [$evaluationId]);
    
    // Insert new learning paths
    if (!empty($_POST['learning_paths'])) {
        foreach ($_POST['learning_paths'] as $index => $path) {
            // Use custom value if provided, otherwise use selected template
            $learningOutcomes = '';
            if (!empty($path['learning_outcomes_custom'])) {
                $learningOutcomes = $path['learning_outcomes_custom'];
            } elseif (!empty($path['learning_outcomes']) && $path['learning_outcomes'] !== '__custom__') {
                $learningOutcomes = $path['learning_outcomes'];
            }
            
            query(
                "INSERT INTO learning_paths 
                (evaluation_id, course_name, lessons_count, learning_outcomes, topics, display_order) 
                VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $evaluationId,
                    $path['course_name'],
                    $path['lessons_count'],
                    $learningOutcomes,
                    $path['topics'] ?? '',
                    $index
                ]
            );
        }
    }
    
    $_SESSION['message'] = 'Cập nhật đánh giá thành công!';
    $_SESSION['message_type'] = 'success';
    
    header('Location: preview.php?id=' . $evaluationId);
    exit;
}

ob_start();
?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="page-header fade-in">
        <h1><i class="fas fa-edit"></i> CHỈNH SỬA ĐÁNH GIÁ</h1>
        <p>Cập nhật thông tin đánh giá học viên</p>
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
                               value="<?php echo htmlspecialchars($evaluation['full_name']); ?>"
                               required>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="student_type" class="form-label">
                            Loại học viên <span class="required">*</span>
                        </label>
                        <select class="form-select" id="student_type" name="student_type" required>
                            <option value="">-- Chọn loại học viên --</option>
                            <option value="Trẻ em" <?php echo $evaluation['student_type'] === 'Trẻ em' ? 'selected' : ''; ?>>Trẻ em</option>
                            <option value="Thiếu niên" <?php echo $evaluation['student_type'] === 'Thiếu niên' ? 'selected' : ''; ?>>Thiếu niên</option>
                            <option value="Người lớn" <?php echo $evaluation['student_type'] === 'Người lớn' ? 'selected' : ''; ?>>Người lớn</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="student_code" class="form-label">
                            Mã báo danh <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="student_code" 
                               name="student_code"
                               value="<?php echo htmlspecialchars($evaluation['student_code']); ?>"
                               readonly>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SECTION 2: Strengths -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-thumbs-up"></i> CÁC ĐIỂM TỐT
            </div>
            <div class="card-body">
                <div class="criteria-section">
                    <?php foreach ($strengths as $criterion): ?>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="strengths[]" 
                                   value="<?php echo htmlspecialchars($criterion['criteria_text']); ?>"
                                   id="strength_<?php echo $criterion['id']; ?>"
                                   <?php echo in_array($criterion['criteria_text'], $selectedStrengths) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="strength_<?php echo $criterion['id']; ?>">
                                <?php echo htmlspecialchars($criterion['criteria_text']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3">
                    <label for="strengths_evaluation" class="form-label">
                        Đánh giá giác
                    </label>
                    <textarea class="form-control" 
                              id="strengths_evaluation" 
                              name="strengths_evaluation"
                              rows="3"
                              placeholder="Nhập đánh giá giác cho các điểm tốt"><?php echo htmlspecialchars($evaluation['strengths_evaluation'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- SECTION 3: Improvements -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-arrow-up"></i> CÁC ĐIỂM CẦN CẢI THIỆN
            </div>
            <div class="card-body">
                <div class="criteria-section">
                    <?php foreach ($improvements as $criterion): ?>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="improvements[]" 
                                   value="<?php echo htmlspecialchars($criterion['criteria_text']); ?>"
                                   id="improvement_<?php echo $criterion['id']; ?>"
                                   <?php echo in_array($criterion['criteria_text'], $selectedImprovements) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="improvement_<?php echo $criterion['id']; ?>">
                                <?php echo htmlspecialchars($criterion['criteria_text']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3">
                    <label for="improvements_evaluation" class="form-label">
                        Đánh giá giác
                    </label>
                    <textarea class="form-control" 
                              id="improvements_evaluation" 
                              name="improvements_evaluation"
                              rows="3"
                              placeholder="Nhập đánh giá giác cho các điểm cần cải thiện"><?php echo htmlspecialchars($evaluation['improvements_evaluation'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- SECTION 4: Program -->
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
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>"
                                        <?php echo $evaluation['course_id'] == $course['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
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
                               value="<?php echo htmlspecialchars($evaluation['program_name']); ?>"
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
                               value="<?php echo $evaluation['evaluation_date']; ?>"
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
        
        <!-- SECTION 5: Learning Paths -->
        <div class="card fade-in">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-route"></i> LỘ TRÌNH HỌC</span>
                <button type="button" class="btn btn-sm btn-outline-light" id="addPathBtn">
                    <i class="fas fa-plus"></i> Thêm khóa học
                </button>
            </div>
            <div class="card-body">
                <div id="learningPathsContainer">
                    <?php foreach ($learningPaths as $index => $path): ?>
                    <div class="learning-path-row" data-path-index="<?php echo $index; ?>">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Khóa học <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="learning_paths[<?php echo $index; ?>][course_name]"
                                       value="<?php echo htmlspecialchars($path['course_name']); ?>"
                                       required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Số buổi <span class="required">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       name="learning_paths[<?php echo $index; ?>][lessons_count]"
                                       value="<?php echo $path['lessons_count']; ?>"
                                       required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Kết quả học tập <span class="required">*</span></label>
                                <?php 
                                $isCustom = true;
                                if ($learningOutcomeTemplates) {
                                    foreach ($learningOutcomeTemplates as $template) {
                                        if ($path['learning_outcomes'] === $template['template_text']) {
                                            $isCustom = false;
                                            break;
                                        }
                                    }
                                }
                                ?>
                                <select class="form-select learning-outcomes-select" 
                                        name="learning_paths[<?php echo $index; ?>][learning_outcomes]"
                                        data-path-index="<?php echo $index; ?>"
                                        required>
                                    <option value="">-- Chọn kết quả học tập --</option>
                                    <?php if ($learningOutcomeTemplates): ?>
                                        <?php foreach ($learningOutcomeTemplates as $template): ?>
                                            <option value="<?php echo htmlspecialchars($template['template_text']); ?>"
                                                    <?php echo ($path['learning_outcomes'] === $template['template_text']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($template['template_text']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="__custom__" <?php echo $isCustom ? 'selected' : ''; ?>>-- Tùy chỉnh --</option>
                                </select>
                                <textarea class="form-control mt-2 learning-outcomes-custom" 
                                          name="learning_paths[<?php echo $index; ?>][learning_outcomes_custom]"
                                          rows="2"
                                          placeholder="Nhập kết quả học tập tùy chỉnh"
                                          style="display: <?php echo $isCustom ? 'block' : 'none'; ?>;"><?php echo $isCustom ? htmlspecialchars($path['learning_outcomes']) : ''; ?></textarea>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-sm remove-path-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-11">
                                <label class="form-label">Chủ đề giao tiếp</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="learning_paths[<?php echo $index; ?>][topics]"
                                       value="<?php echo htmlspecialchars($path['topics'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- SECTION 6: Summary -->
        <div class="card fade-in">
            <div class="card-header">
                <i class="fas fa-clipboard-check"></i> TỔNG KẾT
            </div>
            <div class="card-body">
                <label for="summary" class="form-label">Ghi chú thêm</label>
                <textarea class="form-control" 
                          id="summary" 
                          name="summary"
                          rows="4"><?php echo htmlspecialchars($evaluation['summary'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="card fade-in">
            <div class="card-body text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Lưu Thay Đổi
                </button>
                <a href="preview.php?id=<?php echo $evaluationId; ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </div>
        
    </form>
</div>

<script>
// Set initial path counter based on existing paths
let pathCounter = <?php echo count($learningPaths); ?>;
</script>

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
<script src="../assets/js/validation.js"></script>';
include '../includes/header.php';
echo $content;
include '../includes/footer.php';
?>


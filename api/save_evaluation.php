<?php
/**
 * API: Save Evaluation
 * Creates new evaluation record
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed');
}

try {
    // Validate required fields
    $required = [
        'full_name' => $_POST['full_name'] ?? '',
        'student_type' => $_POST['student_type'] ?? '',
        'student_code' => $_POST['student_code'] ?? '',
        'course_id' => $_POST['course_id'] ?? '',
        'program_name' => $_POST['program_name'] ?? '',
        'evaluation_date' => $_POST['evaluation_date'] ?? ''
    ];
    
    $errors = validateRequired($required);
    
    if (!empty($errors)) {
        errorResponse('Vui lòng điền đầy đủ thông tin bắt buộc', $errors);
    }
    
    // Validate strengths and improvements
    $strengths = $_POST['strengths'] ?? [];
    $improvements = $_POST['improvements'] ?? [];
    
    if (empty($strengths)) {
        errorResponse('Vui lòng chọn ít nhất một điểm tốt');
    }
    
    if (empty($improvements)) {
        errorResponse('Vui lòng chọn ít nhất một điểm cần cải thiện');
    }
    
    // Validate learning paths
    $learningPaths = $_POST['learning_paths'] ?? [];
    
    if (empty($learningPaths)) {
        errorResponse('Vui lòng thêm ít nhất một lộ trình học');
    }
    
    // Process learning paths
    $processedPaths = [];
    foreach ($learningPaths as $path) {
        if (empty($path['course_name']) || empty($path['lessons_count']) || empty($path['learning_outcomes'])) {
            errorResponse('Thông tin lộ trình học không đầy đủ');
        }
        
        $processedPaths[] = [
            'course_name' => sanitize($path['course_name']),
            'lessons_count' => (int)$path['lessons_count'],
            'learning_outcomes' => sanitize($path['learning_outcomes']),
            'topics' => sanitize($path['topics'] ?? '')
        ];
    }
    
    // Prepare evaluation data
    $evaluationData = [
        'full_name' => sanitize($_POST['full_name']),
        'student_type' => sanitize($_POST['student_type']),
        'student_code' => sanitize($_POST['student_code']),
        'course_id' => (int)$_POST['course_id'],
        'program_name' => sanitize($_POST['program_name']),
        'evaluation_date' => sanitize($_POST['evaluation_date']),
        'teacher_name' => sanitize($_POST['teacher_name'] ?? ''),
        'strengths' => implode("\n• ", $strengths),
        'improvements' => implode("\n• ", $improvements),
        'summary' => sanitize($_POST['summary'] ?? ''),
        'learning_paths' => $processedPaths
    ];
    
    // Create evaluation
    $evaluationId = createEvaluation($evaluationData);
    
    if ($evaluationId) {
        successResponse([
            'evaluation_id' => $evaluationId,
            'message' => 'Đánh giá đã được lưu thành công'
        ], 'Lưu đánh giá thành công!');
    } else {
        errorResponse('Không thể lưu đánh giá. Vui lòng thử lại.');
    }
    
} catch (Exception $e) {
    error_log("Error in save_evaluation.php: " . $e->getMessage());
    errorResponse('Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.');
}


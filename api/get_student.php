<?php
/**
 * API: Get Student by Code
 * Returns student information
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../includes/functions.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed');
}

try {
    $studentCode = $_GET['student_code'] ?? '';
    
    if (empty($studentCode)) {
        errorResponse('Mã báo danh không được để trống');
    }
    
    $student = getStudentByCode(sanitize($studentCode));
    
    if ($student) {
        successResponse($student, 'Tìm thấy thông tin học viên');
    } else {
        errorResponse('Không tìm thấy học viên với mã báo danh này', null);
    }
    
} catch (Exception $e) {
    error_log("Error in get_student.php: " . $e->getMessage());
    errorResponse('Đã xảy ra lỗi hệ thống');
}


<?php
/**
 * API: Get Course by ID
 * Returns course information
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../config/database.php';
require_once '../includes/functions.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed');
}

try {
    $courseId = $_GET['course_id'] ?? '';
    
    if (empty($courseId)) {
        errorResponse('ID khóa học không được để trống');
    }
    
    $course = getCourseById((int)$courseId);
    
    if ($course) {
        successResponse($course, 'Tìm thấy thông tin khóa học');
    } else {
        errorResponse('Không tìm thấy khóa học này', null);
    }
    
} catch (Exception $e) {
    error_log("Error in get_course.php: " . $e->getMessage());
    errorResponse('Đã xảy ra lỗi hệ thống');
}


<?php
/**
 * Helper Functions
 * Kyna English - Student Evaluation System
 */

// Sanitize input
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Validate required fields
function validateRequired($fields) {
    $errors = [];
    foreach ($fields as $field => $value) {
        if (empty($value)) {
            $errors[$field] = "Trường này là bắt buộc";
        }
    }
    return $errors;
}

// Format date
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

// Generate student code
function generateStudentCode() {
    $prefix = date('y');
    $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return $prefix . $random;
}

// Check if student code exists
function studentCodeExists($code) {
    $result = getOne("SELECT id FROM students WHERE student_code = ?", [$code]);
    return $result !== false;
}

// Get all students
function getAllStudents() {
    return getAll("SELECT * FROM students ORDER BY created_at DESC");
}

// Get student by ID
function getStudentById($id) {
    return getOne("SELECT * FROM students WHERE id = ?", [$id]);
}

// Get student by code
function getStudentByCode($code) {
    return getOne("SELECT * FROM students WHERE student_code = ?", [$code]);
}

// Create or get student
function createOrGetStudent($fullName, $studentType, $studentCode) {
    // Check if student exists
    $student = getStudentByCode($studentCode);
    
    if ($student) {
        // Update student info
        query(
            "UPDATE students SET full_name = ?, student_type = ? WHERE student_code = ?",
            [$fullName, $studentType, $studentCode]
        );
        return $student['id'];
    } else {
        // Create new student
        query(
            "INSERT INTO students (full_name, student_type, student_code) VALUES (?, ?, ?)",
            [$fullName, $studentType, $studentCode]
        );
        return getLastInsertId();
    }
}

// Get all evaluation criteria
function getAllCriteria($type = null) {
    if ($type) {
        return getAll(
            "SELECT * FROM evaluation_criteria WHERE criteria_type = ? AND is_active = 1 ORDER BY display_order",
            [$type]
        );
    }
    return getAll("SELECT * FROM evaluation_criteria WHERE is_active = 1 ORDER BY criteria_type, display_order");
}

// Get criteria by type
function getCriteriaByType($type) {
    return getAll(
        "SELECT * FROM evaluation_criteria WHERE criteria_type = ? AND is_active = 1 ORDER BY display_order",
        [$type]
    );
}

// Get all courses
function getAllCourses() {
    return getAll("SELECT * FROM courses WHERE is_active = 1 ORDER BY course_level");
}

// Get course by ID
function getCourseById($id) {
    return getOne("SELECT * FROM courses WHERE id = ?", [$id]);
}

// Create evaluation
function createEvaluation($data) {
    try {
        beginTransaction();
        
        // Create or get student
        $studentId = createOrGetStudent(
            $data['full_name'],
            $data['student_type'],
            $data['student_code']
        );
        
        // Insert evaluation
        query(
            "INSERT INTO evaluations 
            (student_id, teacher_name, course_id, program_name, evaluation_date, strengths, improvements, summary) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $studentId,
                $data['teacher_name'] ?? '',
                $data['course_id'],
                $data['program_name'],
                $data['evaluation_date'],
                $data['strengths'],
                $data['improvements'],
                $data['summary'] ?? ''
            ]
        );
        
        $evaluationId = getLastInsertId();
        
        // Insert learning paths
        if (!empty($data['learning_paths'])) {
            foreach ($data['learning_paths'] as $index => $path) {
                query(
                    "INSERT INTO learning_paths 
                    (evaluation_id, course_name, lessons_count, learning_outcomes, topics, display_order) 
                    VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $evaluationId,
                        $path['course_name'],
                        $path['lessons_count'],
                        $path['learning_outcomes'],
                        $path['topics'] ?? '',
                        $index
                    ]
                );
            }
        }
        
        commit();
        return $evaluationId;
        
    } catch (Exception $e) {
        rollback();
        error_log("Error creating evaluation: " . $e->getMessage());
        return false;
    }
}

// Get evaluation by ID
function getEvaluationById($id) {
    $sql = "
        SELECT 
            e.*,
            s.full_name,
            s.student_type,
            s.student_code,
            c.course_name,
            c.course_level,
            c.total_lessons,
            c.topics as course_topics
        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        WHERE e.id = ?
    ";
    return getOne($sql, [$id]);
}

// Get learning paths by evaluation ID
function getLearningPathsByEvaluationId($evaluationId) {
    return getAll(
        "SELECT * FROM learning_paths WHERE evaluation_id = ? ORDER BY display_order",
        [$evaluationId]
    );
}

// Get all evaluations
function getAllEvaluations($limit = 50, $offset = 0) {
    $sql = "
        SELECT 
            e.id,
            e.evaluation_date,
            e.created_at,
            s.full_name,
            s.student_code,
            s.student_type,
            c.course_name,
            c.course_level
        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        ORDER BY e.created_at DESC
        LIMIT ? OFFSET ?
    ";
    return getAll($sql, [$limit, $offset]);
}

// Search evaluations
function searchEvaluations($keyword) {
    $keyword = "%{$keyword}%";
    $sql = "
        SELECT 
            e.id,
            e.evaluation_date,
            e.created_at,
            s.full_name,
            s.student_code,
            s.student_type,
            c.course_name,
            c.course_level
        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        WHERE s.full_name LIKE ? 
           OR s.student_code LIKE ?
           OR c.course_name LIKE ?
        ORDER BY e.created_at DESC
        LIMIT 50
    ";
    return getAll($sql, [$keyword, $keyword, $keyword]);
}

// Delete evaluation
function deleteEvaluation($id) {
    try {
        beginTransaction();
        
        // Delete learning paths
        query("DELETE FROM learning_paths WHERE evaluation_id = ?", [$id]);
        
        // Delete evaluation
        query("DELETE FROM evaluations WHERE id = ?", [$id]);
        
        commit();
        return true;
    } catch (Exception $e) {
        rollback();
        error_log("Error deleting evaluation: " . $e->getMessage());
        return false;
    }
}

// Update PDF status
function updatePDFStatus($evaluationId, $pdfPath) {
    return query(
        "UPDATE evaluations SET pdf_generated = 1, pdf_path = ? WHERE id = ?",
        [$pdfPath, $evaluationId]
    );
}

// Get evaluations by month/year
function getEvaluationsByMonth($month, $year) {
    $sql = "
        SELECT 
            e.id,
            e.evaluation_date,
            e.created_at,
            e.teacher_name,
            s.full_name,
            s.student_code,
            s.student_type,
            c.course_name,
            c.course_level,
            c.course_code
        FROM evaluations e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        WHERE MONTH(e.evaluation_date) = ? 
          AND YEAR(e.evaluation_date) = ?
        ORDER BY e.evaluation_date DESC, e.created_at DESC
    ";
    return getAll($sql, [$month, $year]);
}

// Get monthly statistics
function getMonthlyStatistics($month, $year) {
    $stats = [];
    
    // Total evaluations
    $total = getOne(
        "SELECT COUNT(*) as total FROM evaluations 
         WHERE MONTH(evaluation_date) = ? AND YEAR(evaluation_date) = ?",
        [$month, $year]
    );
    $stats['total_evaluations'] = $total['total'] ?? 0;
    
    // Total students
    $students = getOne(
        "SELECT COUNT(DISTINCT student_id) as total FROM evaluations 
         WHERE MONTH(evaluation_date) = ? AND YEAR(evaluation_date) = ?",
        [$month, $year]
    );
    $stats['total_students'] = $students['total'] ?? 0;
    
    // By student type
    $byType = getAll(
        "SELECT s.student_type, COUNT(*) as count 
         FROM evaluations e
         JOIN students s ON e.student_id = s.id
         WHERE MONTH(e.evaluation_date) = ? AND YEAR(e.evaluation_date) = ?
         GROUP BY s.student_type",
        [$month, $year]
    );
    $stats['by_type'] = $byType ?: [];
    
    // By course
    $byCourse = getAll(
        "SELECT c.course_name, c.course_level, COUNT(*) as count 
         FROM evaluations e
         JOIN courses c ON e.course_id = c.id
         WHERE MONTH(e.evaluation_date) = ? AND YEAR(e.evaluation_date) = ?
         GROUP BY c.id, c.course_name, c.course_level
         ORDER BY count DESC",
        [$month, $year]
    );
    $stats['by_course'] = $byCourse ?: [];
    
    // By day
    $byDay = getAll(
        "SELECT DATE(e.evaluation_date) as eval_date, COUNT(*) as count 
         FROM evaluations e
         WHERE MONTH(e.evaluation_date) = ? AND YEAR(e.evaluation_date) = ?
         GROUP BY DATE(e.evaluation_date)
         ORDER BY eval_date DESC",
        [$month, $year]
    );
    $stats['by_day'] = $byDay ?: [];
    
    return $stats;
}

// Get all months with evaluations
function getAvailableMonths() {
    return getAll(
        "SELECT DISTINCT 
            YEAR(evaluation_date) as year,
            MONTH(evaluation_date) as month,
            DATE_FORMAT(evaluation_date, '%Y-%m') as year_month,
            DATE_FORMAT(evaluation_date, '%M %Y') as display
         FROM evaluations
         ORDER BY year DESC, month DESC"
    );
}

// JSON response helper
function jsonResponse($data, $success = true, $message = '') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Error response helper
function errorResponse($message, $data = null) {
    jsonResponse($data, false, $message);
}

// Success response helper
function successResponse($data = null, $message = 'Thành công') {
    jsonResponse($data, true, $message);
}


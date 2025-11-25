-- ========================================
-- KYNA ENGLISH - STUDENT EVALUATION SYSTEM
-- Database Schema
-- ========================================

-- Drop existing tables if exists
DROP TABLE IF EXISTS learning_paths;
DROP TABLE IF EXISTS evaluations;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS evaluation_criteria;
DROP TABLE IF EXISTS students;

-- ========================================
-- Table: students
-- ========================================
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(255) NOT NULL,
    student_type ENUM('Trẻ em', 'Thiếu niên', 'Người lớn') NOT NULL,
    student_code VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student_code (student_code),
    INDEX idx_full_name (full_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table: evaluation_criteria
-- ========================================
CREATE TABLE evaluation_criteria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    criteria_text TEXT NOT NULL,
    criteria_type ENUM('strengths', 'improvements') NOT NULL,
    category VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_criteria_type (criteria_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table: courses
-- ========================================
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(255) NOT NULL,
    course_code VARCHAR(50) NOT NULL,
    course_level VARCHAR(50) NOT NULL,
    total_lessons INT NOT NULL DEFAULT 32,
    topics TEXT,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_course_level (course_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table: evaluations
-- ========================================
CREATE TABLE evaluations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    teacher_name VARCHAR(255),
    course_id INT NOT NULL,
    program_name VARCHAR(255),
    evaluation_date DATE NOT NULL,
    strengths TEXT,
    improvements TEXT,
    summary TEXT,
    pdf_generated TINYINT(1) DEFAULT 0,
    pdf_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT,
    INDEX idx_evaluation_date (evaluation_date),
    INDEX idx_student_id (student_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Table: learning_paths
-- ========================================
CREATE TABLE learning_paths (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evaluation_id INT NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    lessons_count INT NOT NULL,
    learning_outcomes TEXT,
    topics TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE,
    INDEX idx_evaluation_id (evaluation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Insert Sample Data: evaluation_criteria
-- ========================================

-- STRENGTHS (Các điểm tốt)
INSERT INTO evaluation_criteria (criteria_text, criteria_type, category, display_order) VALUES
('Học viên có thể trả lời các câu hỏi cơ bản về bản thân: Family, Education, Freetime, Hobby, Jobs, Means of Transport, Accommodation,...', 'strengths', 'Communication', 1),
('Học viên đã biết một một số từ vựng chủ đề: Family, Vehicles, Places, Food, School,...', 'strengths', 'Vocabulary', 2),
('Học viên có vốn ngữ pháp để nói về các chủ đề: Thì hiện tại đơn, Thì hiện tại tiếp diễn,...', 'strengths', 'Grammar', 3),
('Học viên có thể vận dụng một số từ nối để làm câu trả lời trở nên mạch lạc và logic hơn: And/Or, Because,...', 'strengths', 'Linking Words', 4),
('Học viên phát âm còn mắc lỗi nhưng không ảnh hưởng nghiêm trọng trong đến sự nghe hiểu của người nghe', 'strengths', 'Pronunciation', 5);

-- IMPROVEMENTS (Các điểm cần cải thiện)
INSERT INTO evaluation_criteria (criteria_text, criteria_type, category, display_order) VALUES
('Học viên chưa có vốn từ vựng nói về các chủ đề quen thuộc: Future plans, Describing places,...', 'improvements', 'Vocabulary', 1),
('Học viên nên sử dụng thêm một số từ nối để làm câu trả lời trở nên mạch lạc và logic hơn: Although, Though, However, Therefore,...', 'improvements', 'Linking Words', 2),
('Học viên cần cũng có phát âm một số từ: Final sounds, /u:/, /sch/, /k/, /t/,...', 'improvements', 'Pronunciation', 3),
('Học viên cần cũng có nhiều về phát âm, ngữ điệu còn bị ảnh hưởng phát âm bản địa', 'improvements', 'Intonation', 4);

-- ========================================
-- Insert Sample Data: courses
-- ========================================
INSERT INTO courses (course_name, course_code, course_level, total_lessons, topics, description) VALUES
('Daily English - Cấp độ DE Beginner 1', 'DE_BEG1', 'Beginner 1', 32, 
'Personal Information, Everyday Life, Getting around, Hobbies and Interests, Relationships, Well-being, Vacations, Entertainment, Expressing yourself, Seasons', 
'Học từ vựng và cấu trúc ngữ pháp cho mục đích giao tiếp trong cuộc sống hàng ngày ở cấp độ A1 và đầu A2 theo chuẩn CEFR'),

('Daily English - Cấp độ DE Beginner 2', 'DE_BEG2', 'Beginner 2', 32,
'Personal Information, Everyday Life, Getting around, Hobbies and Interests, Relationships, Well-being, Vacations, Entertainment, Expressing yourself, Seasons, Food, Health, Jobs, Traveling, Technology',
'Học tập trung vào phần xa Nghe-Nói, giao tiếp các tình huống trong cuộc sống hàng ngày'),

('Daily English - Cấp độ DE Pre-Inter 1', 'DE_PREINT1', 'Pre-Intermediate 1', 32,
'Personal Information, Everyday Life, Getting around, Hobbies and Interests, Relationships, Well-being, Vacations, Entertainment, Expressing yourself, Seasons, Food, Health, Jobs, Traveling, Technology',
'Học tập trung vào phần xa Nghe-Nói, giao tiếp các tình huống trong cuộc sống hàng ngày'),

('Daily English - Cấp độ DE Pre-Inter 2', 'DE_PREINT2', 'Pre-Intermediate 2', 32,
'Advanced Communication Topics',
'Học tập nâng cao kỹ năng giao tiếp tiếng Anh'),

('Daily English - Cấp độ DE Intermediate 1', 'DE_INT1', 'Intermediate 1', 32,
'Complex Communication Scenarios',
'Phát triển kỹ năng giao tiếp ở mức trung cấp');

-- ========================================
-- Insert Sample Students (for testing)
-- ========================================
INSERT INTO students (full_name, student_type, student_code) VALUES
('Nguyễn Văn A', 'Trẻ em', '7001'),
('Trần Thị B', 'Thiếu niên', '7002'),
('Lê Văn C', 'Người lớn', '7003');

-- ========================================
-- Create Views for Reports
-- ========================================
CREATE OR REPLACE VIEW v_evaluation_summary AS
SELECT 
    e.id as evaluation_id,
    s.full_name,
    s.student_code,
    s.student_type,
    c.course_name,
    c.course_level,
    e.evaluation_date,
    e.teacher_name,
    e.created_at
FROM evaluations e
JOIN students s ON e.student_id = s.id
JOIN courses c ON e.course_id = c.id
ORDER BY e.created_at DESC;


/**
 * Form Validation and Handling
 * Kyna English Evaluation System
 */

$(document).ready(function() {
    
    // ========================================
    // LEARNING PATH MANAGEMENT
    // ========================================
    
    let pathCounter = 1;
    
    // Add new learning path row
    $('#addPathBtn').on('click', function() {
        const pathRow = createPathRow(pathCounter);
        $('#learningPathsContainer').append(pathRow);
        pathCounter++;
    });
    
    // Remove learning path row
    $(document).on('click', '.remove-path-btn', function() {
        if ($('.learning-path-row').length > 1) {
            $(this).closest('.learning-path-row').remove();
        } else {
            showAlert('Phải có ít nhất một lộ trình học', 'warning');
        }
    });
    
    // Create learning path row HTML
    function createPathRow(index) {
        return `
            <div class="learning-path-row" data-path-index="${index}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Khóa học <span class="required">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               name="learning_paths[${index}][course_name]"
                               placeholder="Ví dụ: DE Beginner 1"
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Số buổi <span class="required">*</span></label>
                        <input type="number" 
                               class="form-control" 
                               name="learning_paths[${index}][lessons_count]"
                               min="1"
                               value="32"
                               required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Kết quả học tập <span class="required">*</span></label>
                        <textarea class="form-control" 
                                  name="learning_paths[${index}][learning_outcomes]"
                                  rows="1"
                                  placeholder="Mô tả kết quả học tập dự kiến"
                                  required></textarea>
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
                               name="learning_paths[${index}][topics]"
                               placeholder="Các chủ đề giao tiếp sẽ học">
                    </div>
                </div>
            </div>
        `;
    }
    
    // ========================================
    // AUTO FILL STUDENT CODE
    // ========================================
    
    // Get base path
    const basePath = window.location.pathname.includes('/pages/') ? '../' : '';
    
    $('#student_code').on('blur', function() {
        const studentCode = $(this).val();
        
        if (studentCode) {
            // Check if student exists
            $.ajax({
                url: basePath + 'api/get_student.php',
                method: 'GET',
                data: { student_code: studentCode },
                success: function(response) {
                    if (response.success && response.data) {
                        // Auto-fill student information
                        $('#full_name').val(response.data.full_name);
                        $('#student_type').val(response.data.student_type);
                        
                        showAlert('Đã tìm thấy thông tin học viên', 'info');
                    }
                }
            });
        }
    });
    
    // ========================================
    // COURSE SELECTION
    // ========================================
    
    $('#course_id').on('change', function() {
        const courseId = $(this).val();
        
        if (courseId) {
            // Get course details
            $.ajax({
                url: basePath + 'api/get_courses.php',
                method: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    if (response.success && response.data) {
                        // Auto-fill program name
                        $('#program_name').val(response.data.course_name);
                        
                        // Update learning path if empty
                        const firstPathCourse = $('input[name="learning_paths[0][course_name]"]');
                        if (!firstPathCourse.val()) {
                            firstPathCourse.val(response.data.course_name);
                            $('input[name="learning_paths[0][lessons_count]"]').val(response.data.total_lessons);
                            
                            if (response.data.topics) {
                                $('input[name="learning_paths[0][topics]"]').val(response.data.topics);
                            }
                        }
                    }
                }
            });
        }
    });
    
    // ========================================
    // FORM SUBMISSION
    // ========================================
    
    $('#evaluationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            showAlert('Vui lòng điền đầy đủ thông tin bắt buộc', 'danger');
            return;
        }
        
        // Check if at least one strength is selected
        const strengthsChecked = $('input[name="strengths[]"]:checked').length;
        if (strengthsChecked === 0) {
            showAlert('Vui lòng chọn ít nhất một điểm tốt', 'warning');
            scrollToElement('strengthsSection');
            return;
        }
        
        // Check if at least one improvement is selected
        const improvementsChecked = $('input[name="improvements[]"]:checked').length;
        if (improvementsChecked === 0) {
            showAlert('Vui lòng chọn ít nhất một điểm cần cải thiện', 'warning');
            scrollToElement('improvementsSection');
            return;
        }
        
        showLoading('Đang lưu đánh giá...');
        
        // Serialize form data
        const formData = $(this).serialize();
        
        // Submit form
        $.ajax({
            url: basePath + 'api/save_evaluation.php',
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    showAlert('Lưu đánh giá thành công!', 'success');
                    
                    // Redirect to preview page after 1 second
                    setTimeout(function() {
                        window.location.href = basePath + `pages/preview.php?id=${response.data.evaluation_id}`;
                    }, 1000);
                } else {
                    showAlert(response.message || 'Có lỗi xảy ra', 'danger');
                }
            },
            error: function() {
                hideLoading();
                showAlert('Không thể kết nối đến server', 'danger');
            }
        });
    });
    
    // ========================================
    // PREVIEW BEFORE SUBMIT
    // ========================================
    
    $('#previewBtn').on('click', function() {
        // Validate form first
        const form = $('#evaluationForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Open preview in new window (optional)
        // For now, just submit the form
        $('#evaluationForm').submit();
    });
    
    // ========================================
    // CHARACTER COUNTER
    // ========================================
    
    $('textarea[maxlength]').each(function() {
        const maxLength = $(this).attr('maxlength');
        const counterId = $(this).attr('id') + '_counter';
        
        $(this).after(`<small class="text-muted float-end" id="${counterId}">0 / ${maxLength}</small>`);
        
        $(this).on('input', function() {
            const currentLength = $(this).val().length;
            $(`#${counterId}`).text(`${currentLength} / ${maxLength}`);
        });
    });
    
    // ========================================
    // SELECT ALL CHECKBOXES
    // ========================================
    
    $('.select-all-strengths').on('click', function() {
        $('input[name="strengths[]"]').prop('checked', true);
    });
    
    $('.deselect-all-strengths').on('click', function() {
        $('input[name="strengths[]"]').prop('checked', false);
    });
    
    $('.select-all-improvements').on('click', function() {
        $('input[name="improvements[]"]').prop('checked', true);
    });
    
    $('.deselect-all-improvements').on('click', function() {
        $('input[name="improvements[]"]').prop('checked', false);
    });
    
});


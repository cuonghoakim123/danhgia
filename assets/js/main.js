/**
 * Main JavaScript for 123 English Evaluation System
 */

// Document ready
$(document).ready(function() {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Show loading spinner
function showLoading(message = 'Đang xử lý...') {
    const spinner = `
        <div class="spinner-overlay" id="loadingSpinner">
            <div class="text-center">
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-white mt-3">${message}</p>
            </div>
        </div>
    `;
    $('body').append(spinner);
}

// Hide loading spinner
function hideLoading() {
    $('#loadingSpinner').remove();
}

// Show alert message
function showAlert(message, type = 'success') {
    const alertClass = `alert-${type}`;
    const icon = {
        'success': 'fa-check-circle',
        'danger': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('.main-content .container').prepend(alert);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// Validate form fields
function validateForm(formId) {
    let isValid = true;
    const form = $(`#${formId}`);
    
    // Remove previous error messages
    form.find('.is-invalid').removeClass('is-invalid');
    form.find('.invalid-feedback').remove();
    
    // Check required fields
    form.find('[required]').each(function() {
        const field = $(this);
        const value = field.val();
        
        if (!value || value.trim() === '') {
            isValid = false;
            field.addClass('is-invalid');
            field.after('<div class="invalid-feedback">Trường này là bắt buộc</div>');
        }
    });
    
    return isValid;
}

// Format date to DD/MM/YYYY
function formatDate(date) {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

// Confirm delete action
function confirmDelete(message = 'Bạn có chắc chắn muốn xóa?') {
    return confirm(message);
}

// Copy text to clipboard
function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    showAlert('Đã sao chép vào clipboard', 'success');
}

// Smooth scroll to element
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Print element
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('<link rel="stylesheet" href="assets/css/print.css">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(element.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }
}

// AJAX Error Handler
function handleAjaxError(xhr, status, error) {
    // Only log real AJAX errors, not 404s from browser extensions
    if (xhr.status !== 0 || !error.includes('extension')) {
        console.error('AJAX Error:', status, error);
    }
    hideLoading();
    
    let message = 'Đã xảy ra lỗi. Vui lòng thử lại.';
    
    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    }
    
    // Only show alert for real errors, not extension interference
    if (xhr.status !== 0 && xhr.status !== 404) {
        showAlert(message, 'danger');
    }
}

// Setup AJAX defaults
$.ajaxSetup({
    error: handleAjaxError
});

// Export functions to global scope
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showAlert = showAlert;
window.validateForm = validateForm;
window.formatDate = formatDate;
window.confirmDelete = confirmDelete;
window.copyToClipboard = copyToClipboard;
window.scrollToElement = scrollToElement;
window.debounce = debounce;
window.printElement = printElement;


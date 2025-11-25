# Kyna English - Hệ Thống Đánh Giá Học Viên

Hệ thống quản lý và tạo báo cáo đánh giá học viên tiếng Anh chuyên nghiệp cho Kyna English.

![Kyna English Logo](assets/images/logo.svg)

## 🎯 Tính Năng Chính

- ✅ **Form nhập liệu dễ sử dụng**: Giao diện thân thiện cho giáo viên
- ✅ **Quản lý học viên**: Lưu trữ và tra cứu thông tin học viên
- ✅ **Tiêu chí đánh giá linh hoạt**: Tùy chỉnh các tiêu chí điểm tốt và cần cải thiện
- ✅ **Lộ trình học tập**: Xây dựng lộ trình học tùy chỉnh cho từng học viên
- ✅ **Xuất PDF chuyên nghiệp**: Tạo báo cáo PDF đẹp mắt, in ấn được
- ✅ **Tìm kiếm và quản lý**: Dễ dàng tìm kiếm và quản lý các đánh giá đã tạo

## 📋 Yêu Cầu Hệ Thống

- **PHP**: 7.4 trở lên
- **MySQL/MariaDB**: 5.7 trở lên
- **Apache/Nginx**: Web server
- **TCPDF**: Thư viện tạo PDF (đã tích hợp)

### Khuyến Nghị
- **XAMPP**: 8.0+ (Windows/Mac/Linux)
- **Composer**: Để quản lý dependencies (tùy chọn)

## 🚀 Cài Đặt

### Bước 1: Clone/Download Project

```bash
# Clone project vào thư mục htdocs của XAMPP
cd C:\xampp\htdocs\
git clone [repository-url] webstieenghlish

# Hoặc download và giải nén vào thư mục htdocs/webstieenghlish
```

### Bước 2: Tạo Database

**Cách 1: Sử dụng phpMyAdmin (Khuyến nghị)**
1. Mở **phpMyAdmin** (http://localhost/phpmyadmin)
2. Click **"New"** ở sidebar
3. Database name: `kyna_english`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**
6. Chọn database `kyna_english`
7. Click tab **"Import"**
8. Chọn file `database/schema.sql`
9. Click **"Go"**

**Cách 2: Sử dụng MySQL Command Line**
```bash
cd C:\xampp\mysql\bin
mysql.exe -u root -e "CREATE DATABASE kyna_english CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql.exe -u root kyna_english < C:\xampp\htdocs\webstieenghlish\database\schema.sql
```

### Bước 3: Cấu Hình Database

Mở file `config/database.php` và cập nhật thông tin kết nối:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'kyna_english');
define('DB_USER', 'root');        // Thay đổi nếu cần
define('DB_PASS', '');             // Thay đổi nếu cần
```

### Bước 4: Cài Đặt TCPDF

**Cách 1: Download thủ công**

1. Tải TCPDF từ: https://github.com/tecnickcom/TCPDF/archive/refs/heads/main.zip
2. Giải nén và đổi tên thư mục thành `tcpdf`
3. Copy vào `vendor/tcpdf/`

**Cách 2: Sử dụng Composer (khuyến nghị)**

```bash
cd C:\xampp\htdocs\webstieenghlish
composer require tecnickcom/tcpdf
```

### Bước 5: Tạo Logo (Tùy chọn)

Đặt logo của bạn vào:
- `assets/images/logo.png` (PNG, khuyến nghị 200x80px)
- `assets/images/logo.svg` (SVG đã được tạo sẵn)

### Bước 6: Phân Quyền Thư Mục

```bash
# Trên Linux/Mac
chmod -R 755 .
chmod -R 777 pdf_output

# Trên Windows: Click phải -> Properties -> Security -> Edit
# Cho phép Full Control cho Users trên thư mục pdf_output
```

### Bước 7: Truy Cập Hệ Thống

Mở trình duyệt và truy cập:
```
http://localhost/webstieenghlish/
```

## 📁 Cấu Trúc Dự Án

```
webstieenghlish/
│
├── assets/
│   ├── css/
│   │   ├── style.css           # CSS chính
│   │   └── print.css           # CSS cho PDF
│   ├── js/
│   │   ├── main.js             # JavaScript utilities
│   │   └── validation.js       # Form validation
│   └── images/
│       ├── logo.png            # Logo chính
│       └── logo.svg            # Logo vector
│
├── config/
│   └── database.php            # Cấu hình database
│
├── includes/
│   ├── header.php              # Header template
│   ├── footer.php              # Footer template
│   └── functions.php           # Helper functions
│
├── database/
│   └── schema.sql              # Database schema
│
├── vendor/
│   ├── tcpdf/                  # TCPDF library
│   └── pdf_generator.php       # PDF generator class
│
├── pages/
│   ├── preview.php             # Xem trước đánh giá
│   ├── generate_pdf.php        # Tạo PDF
│   ├── list.php                # Danh sách đánh giá
│   └── manage_data.php         # Quản lý dữ liệu
│
├── api/
│   ├── save_evaluation.php     # API lưu đánh giá
│   ├── get_student.php         # API lấy thông tin học viên
│   └── get_courses.php         # API lấy khóa học
│
├── pdf_output/                 # Thư mục chứa PDF đã tạo
│
├── index.php                   # Trang chủ - Form tạo đánh giá
└── README.md                   # File này
```

## 🎓 Hướng Dẫn Sử Dụng

### 1. Tạo Đánh Giá Mới

1. Truy cập trang chủ
2. Điền thông tin học viên (Họ tên, Loại, Mã báo danh)
3. Chọn các điểm tốt từ danh sách
4. Chọn các điểm cần cải thiện
5. Chọn chương trình học
6. Thêm lộ trình học (có thể thêm nhiều khóa)
7. Click "Lưu và Xem Trước"

### 2. Xuất PDF

1. Từ trang xem trước, click "Xuất PDF"
2. PDF sẽ được mở trong tab mới
3. Có thể download hoặc in trực tiếp

### 3. Quản Lý Đánh Giá

- **Danh sách**: Xem tất cả đánh giá đã tạo
- **Tìm kiếm**: Tìm theo tên, mã báo danh, khóa học
- **Xem/Sửa/Xóa**: Quản lý các đánh giá

### 4. Quản Lý Dữ Liệu

- **Khóa học**: Thêm/xóa các khóa học
- **Tiêu chí đánh giá**: Thêm/xóa tiêu chí

## 🎨 Tùy Chỉnh

### Thay Đổi Màu Sắc

Mở `assets/css/style.css` và chỉnh sửa:

```css
:root {
    --primary-color: #52c166;      /* Màu chính */
    --secondary-color: #ff69b4;    /* Màu phụ */
    /* ... */
}
```

### Thêm Tiêu Chí Đánh Giá

1. Truy cập "Quản Lý Dữ Liệu"
2. Tab "Tiêu Chí Đánh Giá"
3. Điền form và thêm mới

### Tùy Chỉnh Template PDF

Mở `vendor/pdf_generator.php` và chỉnh sửa:
- Layout
- Font size
- Màu sắc
- Nội dung sections

## 🔧 Xử Lý Sự Cố

### Lỗi Database Connection

```
Database connection failed: ...
```

**Giải pháp:**
- Kiểm tra MySQL đã chạy chưa (XAMPP Control Panel)
- Kiểm tra thông tin trong `config/database.php`
- Đảm bảo database `kyna_english` đã được tạo

### Lỗi TCPDF Not Found

```
Thư viện TCPDF chưa được cài đặt
```

**Giải pháp:**
- Cài đặt TCPDF theo Bước 4 ở trên
- Kiểm tra đường dẫn `vendor/tcpdf/tcpdf.php`

### Lỗi Không Tạo Được PDF

```
Cannot write file pdf_output/...
```

**Giải pháp:**
- Kiểm tra quyền ghi của thư mục `pdf_output/`
- Windows: Click phải -> Properties -> Security
- Linux/Mac: `chmod 777 pdf_output`

### Lỗi Layout/CSS

**Giải pháp:**
- Xóa cache trình duyệt (Ctrl + Shift + Delete)
- Hard refresh (Ctrl + F5)

## 🔒 Bảo Mật

- ✅ Prepared statements (SQL Injection protection)
- ✅ Input sanitization
- ✅ XSS protection
- ✅ CSRF token (có thể thêm nếu cần)
- ⚠️ **Khuyến nghị**: Không deploy trực tiếp lên production mà không có authentication

## 📊 Database Schema

### Bảng Chính

- **students**: Thông tin học viên
- **evaluations**: Đánh giá
- **courses**: Khóa học
- **evaluation_criteria**: Tiêu chí đánh giá
- **learning_paths**: Lộ trình học

Xem chi tiết trong `database/schema.sql`

## 🤝 Đóng Góp

Mọi đóng góp đều được hoan nghênh!

1. Fork project
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

## 📝 License

Copyright © 2024 Kyna English. All rights reserved.

## 📞 Liên Hệ

- **Phone**: 1900 6364 09
- **Email**: hotro@kynaforkids.vn
- **Website**: [kynaforkids.vn](https://kynaforkids.vn)

## 🎉 Credits

Developed with ❤️ for Kyna English

### Technologies Used

- PHP 7.4+
- MySQL
- Bootstrap 5
- jQuery
- TCPDF
- Font Awesome

---

**Made with 💚 by Kyna English Team**


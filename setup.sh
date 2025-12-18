#!/bin/bash
# Setup Script for Linux/Mac
# 123 English Evaluation System

echo "========================================="
echo "123 English - Setup Script"
echo "========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${YELLOW}Warning: Not running as root. Some operations may fail.${NC}"
    echo "Consider running: sudo ./setup.sh"
    echo ""
fi

# Step 1: Check requirements
echo "Step 1: Checking requirements..."
echo "-----------------------------------"

# Check PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)
    echo -e "${GREEN}✓${NC} PHP found: $PHP_VERSION"
else
    echo -e "${RED}✗${NC} PHP not found. Please install PHP 7.4 or higher."
    exit 1
fi

# Check MySQL
if command -v mysql &> /dev/null; then
    MYSQL_VERSION=$(mysql --version | cut -d " " -f 3)
    echo -e "${GREEN}✓${NC} MySQL found: $MYSQL_VERSION"
else
    echo -e "${RED}✗${NC} MySQL not found. Please install MySQL."
    exit 1
fi

# Check Composer (optional)
if command -v composer &> /dev/null; then
    echo -e "${GREEN}✓${NC} Composer found"
else
    echo -e "${YELLOW}!${NC} Composer not found (optional)"
fi

echo ""

# Step 2: Set permissions
echo "Step 2: Setting file permissions..."
echo "-----------------------------------"

chmod -R 755 .
chmod 777 pdf_output
echo -e "${GREEN}✓${NC} Permissions set"
echo ""

# Step 3: Database setup
echo "Step 3: Database setup..."
echo "-----------------------------------"

read -p "MySQL root password: " -s MYSQL_PASS
echo ""
read -p "Create database 'kyna_english'? (y/n): " CREATE_DB

if [ "$CREATE_DB" = "y" ]; then
    mysql -u root -p$MYSQL_PASS -e "CREATE DATABASE IF NOT EXISTS kyna_english CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} Database created"
        
        # Import schema
        read -p "Import database schema? (y/n): " IMPORT_SCHEMA
        
        if [ "$IMPORT_SCHEMA" = "y" ]; then
            mysql -u root -p$MYSQL_PASS kyna_english < database/schema.sql
            
            if [ $? -eq 0 ]; then
                echo -e "${GREEN}✓${NC} Schema imported successfully"
            else
                echo -e "${RED}✗${NC} Failed to import schema"
            fi
        fi
    else
        echo -e "${RED}✗${NC} Failed to create database"
    fi
fi

echo ""

# Step 4: Install dependencies
echo "Step 4: Installing dependencies..."
echo "-----------------------------------"

if command -v composer &> /dev/null; then
    read -p "Install TCPDF via Composer? (y/n): " INSTALL_TCPDF
    
    if [ "$INSTALL_TCPDF" = "y" ]; then
        composer require tecnickcom/tcpdf
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓${NC} TCPDF installed"
        else
            echo -e "${RED}✗${NC} Failed to install TCPDF"
        fi
    fi
else
    echo -e "${YELLOW}!${NC} Composer not available. Please install TCPDF manually."
    echo "  Download: https://github.com/tecnickcom/TCPDF/archive/refs/heads/main.zip"
    echo "  Extract to: vendor/tcpdf/"
fi

echo ""

# Step 5: Summary
echo "========================================="
echo "Setup Complete!"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Update database credentials in config/database.php"
echo "2. Place your logo at assets/images/logo.png"
echo "3. Start your web server"
echo "4. Access: http://localhost/webstieenghlish/"
echo ""
echo -e "${GREEN}Happy coding!${NC}"


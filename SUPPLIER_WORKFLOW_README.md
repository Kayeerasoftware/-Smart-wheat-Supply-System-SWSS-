# Supplier Workflow System with PDF Validation

This document describes the complete supplier onboarding and access control system implemented in the SWSS (Supply Chain Management) application.

## 🎯 Overview

The system implements a comprehensive supplier workflow that includes:

1. **PDF Document Validation** - Java server validates supplier PDFs for required sections
2. **Access Control** - Restricted dashboard access based on approval status
3. **Facility Visit Management** - Admin-scheduled visits with approval workflow
4. **Chat Restrictions** - Limited communication based on approval status

## 🔐 Supplier Registration & PDF Validation Workflow

### Step 1: Supplier Registration
- Supplier registers with basic information
- **Required**: Upload PDF document (company registration/license)
- PDF must contain 6 required sections (see below)

### Step 2: PDF Validation (Java Server)
The Java server validates PDFs for the following required sections:

1. **Company Information** - Business details, contact information, registration
2. **Financial Stability** - Revenue, assets, financial statements
3. **Business Reputation** - References, certifications, experience
4. **Regulatory Compliance** - Licenses, permits, certifications
5. **Product/Service Summary** - Offerings, inventory, specialties
6. **Declaration** - Legal statements, signatures, confirmations

**Validation Criteria:**
- Minimum 60% overall score required
- Each section must score at least 30% to be considered present
- Keyword-based content analysis

### Step 3: Access Levels Based on Validation

#### 📊 Pending Status (PDF not validated)
- **Access**: None - cannot access any system features
- **Dashboard**: Shows application status and contact information
- **Actions**: Upload corrected PDF if validation failed

#### 📊 Basic Access (PDF validated, awaiting facility visit)
- **Access**: Limited dashboard, admin chat only, notifications
- **Features Available**:
  - View application status
  - Chat with administrators only
  - Receive notifications
  - View facility visit schedule
- **Features Restricted**:
  - Inventory management
  - Orders processing
  - Reports viewing
  - Chat with other users
  - Payments, deliveries, contracts

#### ✅ Full Access (Facility visit approved)
- **Access**: Complete system access
- **All Features Available**:
  - Full inventory management
  - Order processing
  - Reports and analytics
  - Chat with all users
  - Payments, deliveries, contracts

## 🏗️ System Architecture

### Java Server (PDF Validation)
- **Location**: `java-server/`
- **Technology**: Spring Boot 3.2.0, Java 17
- **Dependencies**: Apache PDFBox for PDF processing
- **Port**: 8080

**Key Components:**
- `PdfValidationService` - Core validation logic
- `PdfValidationController` - REST API endpoints
- `PdfValidationRequest/Response` - Data models

**API Endpoints:**
- `POST /api/validation/validate-pdf` - Validate PDF document
- `GET /api/validation/health` - Health check
- `GET /api/validation/required-sections` - Get required sections

### Laravel Application (Main System)
- **Technology**: Laravel 10, PHP 8.1+
- **Database**: MySQL/PostgreSQL

**Key Components:**
- `JavaServerService` - Integration with Java server
- `SupplierAccessMiddleware` - Access control middleware
- `DashboardController` - Dynamic dashboard based on status
- `ChatController` - Restricted chat functionality

## 🚀 Installation & Setup

### 1. Java Server Setup
```bash
cd java-server
mvn clean install
mvn spring-boot:run
```

### 2. Laravel Application Setup
```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage setup
php artisan storage:link
```

### 3. Configuration
Update `.env` file:
```env
# Java Server Configuration
JAVA_SERVER_URL=http://localhost:8080
JAVA_SERVER_TIMEOUT=60
JAVA_SERVER_API_KEY=your_api_key_here

# Vendor Approval Settings
VENDOR_APPROVAL_THRESHOLD=70
```

## 📋 Database Schema

### Vendors Table (Updated)
```sql
vendors table:
- user_id (foreign key)
- application_data (JSON)
- status (enum: pending, pdf_validated, pdf_rejected, pending_visit, approved, rejected)
- processing_status (enum: pending_review, pdf_validation_failed, pending_visit, visit_completed, approved, rejected)
- pdf_paths (JSON)
- image_paths (JSON)
- pdf_validation_result (JSON)
- facility_visit_scheduled (boolean)
- facility_visit_date (timestamp)
- facility_visit_notes (text)
- score_financial (integer)
- score_reputation (integer)
- score_compliance (integer)
- total_score (integer)
```

## 🔧 Usage Examples

### Testing Java Server Integration
```bash
# Test basic connectivity
php artisan test:java-server

# Test with specific supplier
php artisan test:java-server --supplier-id=123
```

### Supplier Registration Flow
1. Supplier visits `/register`
2. Selects "Supplier" role
3. Fills business information
4. Uploads PDF document
5. System automatically validates PDF via Java server
6. Based on validation result:
   - Success → Basic access granted
   - Failure → PDF rejected status, must re-upload

### Admin Facility Visit Management
1. Admin views pending suppliers in admin dashboard
2. Schedules facility visit
3. Supplier receives notification
4. After visit, admin approves/rejects
5. Supplier gains full access if approved

## 🛡️ Security & Access Control

### Middleware Implementation
- `supplier.access:basic` - Requires PDF validation
- `supplier.access:full` - Requires full approval
- `supplier.access:pdf_validated` - Requires PDF validation (no rejection)

### Route Protection
```php
// Example: Inventory routes require full access
Route::middleware(['auth', 'supplier.access:full'])->prefix('inventory')->group(function () {
    // Inventory management routes
});
```

### Chat Restrictions
- **Pending/PDF Rejected**: No chat access
- **Basic Access**: Admin chat only
- **Full Access**: Chat with all users

## 📊 Status Flow Diagram

```
Registration → PDF Upload → Java Validation → Status Decision
                                                      ↓
                                              ┌─────────────┐
                                              │   Pending   │
                                              └─────────────┘
                                                      ↓
                                              ┌─────────────┐
                                              │PDF Validated│
                                              │Basic Access │
                                              └─────────────┘
                                                      ↓
                                              ┌─────────────┐
                                              │Facility     │
                                              │Visit        │
                                              └─────────────┘
                                                      ↓
                                              ┌─────────────┐
                                              │   Approved  │
                                              │ Full Access │
                                              └─────────────┘
```

## 🧪 Testing

### Manual Testing
1. Register as supplier with valid PDF
2. Verify basic access restrictions
3. Test admin facility visit scheduling
4. Verify full access after approval

### Automated Testing
```bash
# Run Laravel tests
php artisan test

# Test Java server integration
php artisan test:java-server
```

## 🔍 Troubleshooting

### Common Issues

1. **Java Server Not Responding**
   - Check if server is running on port 8080
   - Verify firewall settings
   - Check Java server logs

2. **PDF Validation Failing**
   - Ensure PDF contains required sections
   - Check PDF file size (max 2MB)
   - Verify PDF is not corrupted

3. **Access Restrictions Not Working**
   - Check middleware registration
   - Verify vendor status in database
   - Clear application cache

### Debug Commands
```bash
# Check Java server health
curl http://localhost:8080/api/validation/health

# View vendor status
php artisan tinker
>>> App\Models\Vendor::with('user')->get()->pluck('status', 'user_id')
```

## 📈 Future Enhancements

1. **Advanced PDF Analysis**
   - OCR for scanned documents
   - Machine learning for better content detection
   - Fraud detection algorithms

2. **Enhanced Workflow**
   - Multi-step approval process
   - Automated facility visit scheduling
   - Integration with external verification services

3. **Reporting & Analytics**
   - Supplier performance metrics
   - Approval rate analytics
   - Document quality insights

## 📞 Support

For technical support or questions about the supplier workflow system:

- **Documentation**: This README
- **Code Issues**: Check GitHub issues
- **Integration Help**: Review `JavaServerService` class
- **Testing**: Use `TestJavaServerIntegration` command

---

**System Version**: 1.0.0  
**Last Updated**: January 2025  
**Compatibility**: Laravel 10+, Java 17+, Spring Boot 3.2.0 
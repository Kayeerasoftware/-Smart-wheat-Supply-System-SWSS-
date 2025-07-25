# PDF Validation Server

A Java Spring Boot application that validates supplier PDF documents for the SWSS (Supply Chain Management) system.

## Features

- PDF content validation based on required sections
- Keyword-based section detection
- Scoring system for document completeness
- RESTful API endpoints
- Health check and monitoring

## Required PDF Sections

The server validates that supplier PDFs contain the following sections:

1. **Company Information** - Business details, contact information, registration
2. **Financial Stability** - Revenue, assets, financial statements
3. **Business Reputation** - References, certifications, experience
4. **Regulatory Compliance** - Licenses, permits, certifications
5. **Product/Service Summary** - Offerings, inventory, specialties
6. **Declaration** - Legal statements, signatures, confirmations

## Prerequisites

- Java 17 or higher
- Maven 3.6 or higher

## Building the Application

```bash
cd java-server
mvn clean install
```

## Running the Application

```bash
# Run with Maven
mvn spring-boot:run

# Or run the JAR file
java -jar target/pdf-validation-server-1.0.0.jar
```

The server will start on port 8080.

## API Endpoints

### Health Check
```
GET /api/validation/health
```

### Get Required Sections
```
GET /api/validation/required-sections
```

### Validate PDF
```
POST /api/validation/validate-pdf
Content-Type: application/json

{
    "pdfFilePath": "/path/to/supplier/document.pdf",
    "supplierId": "supplier123",
    "businessName": "ABC Company Ltd"
}
```

## Response Format

```json
{
    "valid": true,
    "message": "PDF validation successful. All required sections found.",
    "overallScore": 85,
    "sectionScores": {
        "Company Information": 90,
        "Financial Stability": 85,
        "Business Reputation": 80,
        "Regulatory Compliance": 90,
        "Product/Service Summary": 85,
        "Declaration": 80
    },
    "missingSections": [],
    "validationErrors": [],
    "supplierId": "supplier123",
    "businessName": "ABC Company Ltd"
}
```

## Configuration

Edit `src/main/resources/application.properties` to customize:

- Server port
- Logging levels
- File upload limits
- CORS settings

## Integration with Laravel

This server is designed to work with the Laravel SWSS application. The Laravel app will:

1. Upload supplier PDFs to a shared storage location
2. Call this Java server to validate the PDF content
3. Update supplier status based on validation results
4. Control access to system features based on validation status 
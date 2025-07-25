# Java Server API Specification

This document outlines the API endpoints that the Java server should implement to integrate with the Laravel SWSS application for vendor document processing and scoring.

## Base URL
```
http://localhost:8080
```

## Authentication
All API requests should include an optional Bearer token in the Authorization header:
```
Authorization: Bearer {api_key}
```

## Endpoints

### 1. Health Check
**GET** `/health`

Check if the Java server is running and healthy.

**Response:**
```json
{
  "status": "healthy",
  "timestamp": "2024-06-17T10:30:00Z",
  "version": "1.0.0"
}
```

### 2. Process Vendor Documents
**POST** `/api/process-vendor-documents`

Process vendor PDF documents and return scoring results.

**Request Body:**
```json
{
  "vendor_id": 123,
  "business_name": "ABC Supplies Ltd.",
  "business_type": "supplier",
  "documents": {
    "tax_id": {
      "content": "base64_encoded_pdf_content",
      "filename": "business_registration.pdf",
      "type": "tax_id",
      "size": 1024000
    },
    "financial_records": {
      "content": "base64_encoded_pdf_content",
      "filename": "financial_statements.pdf",
      "type": "financial_records",
      "size": 2048000
    },
    "certifications": {
      "content": "base64_encoded_pdf_content",
      "filename": "iso_certification.pdf",
      "type": "certifications",
      "size": 512000
    },
    "insurance": {
      "content": "base64_encoded_pdf_content",
      "filename": "insurance_certificate.pdf",
      "type": "insurance",
      "size": 768000
    }
  }
}
```

**Response:**
```json
{
  "financial_score": 85.5,
  "reputation_score": 92.0,
  "compliance_score": 88.5,
  "total_score": 88.7,
  "processing_status": "completed",
  "analysis_details": {
    "financial_analysis": {
      "revenue_stability": 90,
      "profit_margins": 85,
      "debt_ratio": 80,
      "cash_flow": 87
    },
    "reputation_analysis": {
      "business_history": 95,
      "market_standing": 90,
      "customer_reviews": 88,
      "industry_recognition": 95
    },
    "compliance_analysis": {
      "regulatory_compliance": 90,
      "certification_validity": 95,
      "insurance_coverage": 85,
      "legal_standing": 92
    }
  },
  "recommendations": [
    "Strong financial position with stable revenue streams",
    "Excellent reputation in the industry",
    "All required certifications are valid and up-to-date"
  ],
  "risk_factors": [
    "Minor compliance issues in insurance documentation"
  ]
}
```

## Scoring Algorithm

The Java server should implement the following scoring algorithm:

### Financial Score (40% weight)
- Revenue stability and growth patterns
- Profit margins and financial ratios
- Debt-to-equity ratio
- Cash flow analysis
- Financial statement consistency

### Reputation Score (30% weight)
- Business history and track record
- Market standing and industry position
- Customer reviews and testimonials
- Industry awards and recognition
- Business references

### Compliance Score (30% weight)
- Regulatory compliance history
- Certification validity and scope
- Insurance coverage adequacy
- Legal standing and litigation history
- Quality management systems

### Total Score Calculation
```
Total Score = (Financial Score × 0.4) + (Reputation Score × 0.3) + (Compliance Score × 0.3)
```

## Error Responses

### 400 Bad Request
```json
{
  "error": "Invalid document format",
  "message": "PDF file is corrupted or invalid",
  "vendor_id": 123
}
```

### 422 Unprocessable Entity
```json
{
  "error": "Document processing failed",
  "message": "Unable to extract text from PDF",
  "vendor_id": 123,
  "failed_documents": ["financial_records"]
}
```

### 500 Internal Server Error
```json
{
  "error": "Processing error",
  "message": "Internal server error during document analysis",
  "vendor_id": 123
}
```

## Implementation Notes

1. **PDF Processing**: The Java server should be able to extract and analyze text from PDF documents.

2. **Scoring Consistency**: Scores should be consistent and reproducible for the same documents.

3. **Performance**: Document processing should complete within 60 seconds.

4. **Fallback Mode**: If the Java server is unavailable, the Laravel application will use default scores and mark for manual review.

5. **Logging**: The Java server should log all processing activities for audit purposes.

6. **Security**: Ensure secure handling of sensitive business documents.

## Testing

Use the Laravel command to test the integration:
```bash
php artisan test:java-server
```

Test with a specific vendor:
```bash
php artisan test:java-server --vendor-id=1
``` 
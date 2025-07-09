package com.swss.controller;

import com.swss.model.PdfValidationRequest;
import com.swss.model.PdfValidationResponse;
import com.swss.service.PdfValidationService;
import jakarta.validation.Valid;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/validation")
@CrossOrigin(origins = "*")
public class PdfValidationController {
    
    private static final Logger logger = LoggerFactory.getLogger(PdfValidationController.class);
    
    @Autowired
    private PdfValidationService pdfValidationService;
    
    @PostMapping("/validate-pdf")
    public ResponseEntity<PdfValidationResponse> validatePdf(@Valid @RequestBody PdfValidationRequest request) {
        logger.info("Received PDF validation request for supplier: {}", request.getSupplierId());
        
        try {
            PdfValidationResponse response = pdfValidationService.validatePdf(
                request.getPdfFilePath(),
                request.getSupplierId(),
                request.getBusinessName()
            );
            
            logger.info("PDF validation completed for supplier {}: valid={}", 
                       request.getSupplierId(), response.isValid());
            
            return ResponseEntity.ok(response);
            
        } catch (Exception e) {
            logger.error("Error processing PDF validation request for supplier {}: {}", 
                        request.getSupplierId(), e.getMessage(), e);
            
            PdfValidationResponse errorResponse = new PdfValidationResponse(
                false, "Internal server error: " + e.getMessage(), 0, 
                null, null, null, request.getSupplierId(), request.getBusinessName()
            );
            
            return ResponseEntity.internalServerError().body(errorResponse);
        }
    }
    
    @GetMapping("/health")
    public ResponseEntity<String> healthCheck() {
        logger.info("Health check requested");
        return ResponseEntity.ok("PDF Validation Server is running");
    }
    
    @GetMapping("/required-sections")
    public ResponseEntity<String[]> getRequiredSections() {
        String[] sections = {
            "Company Information",
            "Financial Stability",
            "Business Reputation", 
            "Regulatory Compliance",
            "Product/Service Summary",
            "Declaration"
        };
        return ResponseEntity.ok(sections);
    }
} 
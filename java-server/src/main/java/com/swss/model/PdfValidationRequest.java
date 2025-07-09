package com.swss.model;

import jakarta.validation.constraints.NotBlank;

public class PdfValidationRequest {
    
    @NotBlank(message = "PDF file path is required")
    private String pdfFilePath;
    
    @NotBlank(message = "Supplier ID is required")
    private String supplierId;
    
    @NotBlank(message = "Business name is required")
    private String businessName;
    
    public PdfValidationRequest() {}
    
    public PdfValidationRequest(String pdfFilePath, String supplierId, String businessName) {
        this.pdfFilePath = pdfFilePath;
        this.supplierId = supplierId;
        this.businessName = businessName;
    }
    
    // Getters and Setters
    public String getPdfFilePath() {
        return pdfFilePath;
    }
    
    public void setPdfFilePath(String pdfFilePath) {
        this.pdfFilePath = pdfFilePath;
    }
    
    public String getSupplierId() {
        return supplierId;
    }
    
    public void setSupplierId(String supplierId) {
        this.supplierId = supplierId;
    }
    
    public String getBusinessName() {
        return businessName;
    }
    
    public void setBusinessName(String businessName) {
        this.businessName = businessName;
    }
} 
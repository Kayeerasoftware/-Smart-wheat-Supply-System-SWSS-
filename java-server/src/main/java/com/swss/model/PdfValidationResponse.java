package com.swss.model;

import java.util.List;
import java.util.Map;

public class PdfValidationResponse {
    
    private boolean isValid;
    private String message;
    private int overallScore;
    private Map<String, Integer> sectionScores;
    private List<String> missingSections;
    private List<String> validationErrors;
    private String supplierId;
    private String businessName;
    
    public PdfValidationResponse() {}
    
    public PdfValidationResponse(boolean isValid, String message, int overallScore, 
                               Map<String, Integer> sectionScores, List<String> missingSections, 
                               List<String> validationErrors, String supplierId, String businessName) {
        this.isValid = isValid;
        this.message = message;
        this.overallScore = overallScore;
        this.sectionScores = sectionScores;
        this.missingSections = missingSections;
        this.validationErrors = validationErrors;
        this.supplierId = supplierId;
        this.businessName = businessName;
    }
    
    // Getters and Setters
    public boolean isValid() {
        return isValid;
    }
    
    public void setValid(boolean valid) {
        isValid = valid;
    }
    
    public String getMessage() {
        return message;
    }
    
    public void setMessage(String message) {
        this.message = message;
    }
    
    public int getOverallScore() {
        return overallScore;
    }
    
    public void setOverallScore(int overallScore) {
        this.overallScore = overallScore;
    }
    
    public Map<String, Integer> getSectionScores() {
        return sectionScores;
    }
    
    public void setSectionScores(Map<String, Integer> sectionScores) {
        this.sectionScores = sectionScores;
    }
    
    public List<String> getMissingSections() {
        return missingSections;
    }
    
    public void setMissingSections(List<String> missingSections) {
        this.missingSections = missingSections;
    }
    
    public List<String> getValidationErrors() {
        return validationErrors;
    }
    
    public void setValidationErrors(List<String> validationErrors) {
        this.validationErrors = validationErrors;
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
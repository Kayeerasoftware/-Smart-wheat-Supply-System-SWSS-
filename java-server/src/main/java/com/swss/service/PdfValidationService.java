package com.swss.service;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import com.swss.model.PdfValidationResponse;

@Service
public class PdfValidationService {
    
    private static final Logger logger = LoggerFactory.getLogger(PdfValidationService.class);
    
    // Required sections for supplier PDF validation
    private static final List<String> REQUIRED_SECTIONS = Arrays.asList(
        "Company Information",
        "Financial Stability", 
        "Business Reputation",
        "Regulatory Compliance",
        "Product/Service Summary",
        "Declaration"
    );
    
    // Keywords for each section to help identify content
    private static final Map<String, List<String>> SECTION_KEYWORDS = new HashMap<>();
    
    static {
        SECTION_KEYWORDS.put("Company Information", Arrays.asList(
            "company", "business", "organization", "corporation", "limited", "inc", "llc",
            "address", "contact", "phone", "email", "website", "registration", "license"
        ));
        
        SECTION_KEYWORDS.put("Financial Stability", Arrays.asList(
            "financial", "revenue", "profit", "income", "assets", "liabilities", "balance sheet",
            "cash flow", "credit", "bank", "accounting", "audit", "tax", "financial statement"
        ));
        
        SECTION_KEYWORDS.put("Business Reputation", Arrays.asList(
            "reputation", "references", "clients", "customers", "partners", "certification",
            "awards", "recognition", "experience", "years", "history", "track record"
        ));
        
        SECTION_KEYWORDS.put("Regulatory Compliance", Arrays.asList(
            "compliance", "regulatory", "license", "permit", "certification", "iso", "fda",
            "government", "authority", "regulation", "standard", "requirement", "approval"
        ));
        
        SECTION_KEYWORDS.put("Product/Service Summary", Arrays.asList(
            "product", "service", "offering", "catalog", "inventory", "supply", "goods",
            "materials", "equipment", "specialty", "category", "description"
        ));
        
        SECTION_KEYWORDS.put("Declaration", Arrays.asList(
            "declaration", "statement", "certify", "confirm", "truth", "accurate", "complete",
            "signature", "date", "authorized", "representative", "officer"
        ));
    }
    
    public PdfValidationResponse validatePdf(String pdfFilePath, String supplierId, String businessName) {
        logger.info("Starting PDF validation for supplier: {} with business: {}", supplierId, businessName);
        
        try {
            // Basic file validation
            File pdfFile = new File(pdfFilePath);
            if (!pdfFile.exists()) {
                return createErrorResponse("PDF file not found: " + pdfFilePath, supplierId, businessName);
            }
            
            if (!pdfFile.canRead()) {
                return createErrorResponse("PDF file cannot be read: " + pdfFilePath, supplierId, businessName);
            }
            
            // Extract text from PDF
            String pdfText = extractTextFromPdf(pdfFile);
            if (pdfText == null || pdfText.trim().isEmpty()) {
                return createErrorResponse("Could not extract text from PDF or PDF is empty", supplierId, businessName);
            }
            
            // Validate PDF content
            return validatePdfContent(pdfText, supplierId, businessName);
            
        } catch (IOException | IllegalArgumentException e) {
            logger.error("Error validating PDF for supplier {}: {}", supplierId, e.getMessage(), e);
            return createErrorResponse("Error processing PDF: " + e.getMessage(), supplierId, businessName);
        }
    }
    
    private String extractTextFromPdf(File pdfFile) throws IOException {
        try (PDDocument document = PDDocument.load(pdfFile)) {
            PDFTextStripper stripper = new PDFTextStripper();
            return stripper.getText(document);
        }
    }
    
    private PdfValidationResponse validatePdfContent(String pdfText, String supplierId, String businessName) {
        String normalizedText = pdfText.toLowerCase();
        Map<String, Integer> sectionScores = new HashMap<>();
        List<String> missingSections = new ArrayList<>();
        List<String> validationErrors = new ArrayList<>();
        
        // Check each required section
        for (String section : REQUIRED_SECTIONS) {
            int sectionScore = calculateSectionScore(section, normalizedText);
            sectionScores.put(section, sectionScore);
            
            if (sectionScore < 30) { // Threshold for considering a section present
                missingSections.add(section);
            }
        }
        
        // Calculate overall score
        int overallScore = sectionScores.values().stream()
                .mapToInt(Integer::intValue)
                .sum() / sectionScores.size();
        
        // Determine if PDF is valid
        boolean isValid = missingSections.isEmpty() && overallScore >= 60;
        
        String message = isValid ? 
            "PDF validation successful. All required sections found." :
            "PDF validation failed. Missing or incomplete sections: " + String.join(", ", missingSections);
        
        logger.info("PDF validation completed for supplier {}: valid={}, score={}, missing={}", 
                   supplierId, isValid, overallScore, missingSections);
        
        return new PdfValidationResponse(
            isValid, message, overallScore, sectionScores, 
            missingSections, validationErrors, supplierId, businessName
        );
    }
    
    private int calculateSectionScore(String section, String normalizedText) {
        List<String> keywords = SECTION_KEYWORDS.get(section);
        if (keywords == null) return 0;
        
        int keywordMatches = 0;
        for (String keyword : keywords) {
            if (normalizedText.contains(keyword.toLowerCase())) {
                keywordMatches++;
            }
        }
        
        // Calculate score based on keyword matches
        double matchPercentage = (double) keywordMatches / keywords.size();
        return (int) (matchPercentage * 100);
    }
    
    private PdfValidationResponse createErrorResponse(String errorMessage, String supplierId, String businessName) {
        logger.error("PDF validation error for supplier {}: {}", supplierId, errorMessage);
        return new PdfValidationResponse(
            false, errorMessage, 0, new HashMap<>(), 
            new ArrayList<>(), Arrays.asList(errorMessage), supplierId, businessName
        );
    }
} 
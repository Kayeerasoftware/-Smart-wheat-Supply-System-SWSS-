# Sample PDF Documents for Supplier Validation Testing

This directory contains sample PDF documents that are guaranteed to pass the PDF validation system for both Uganda and international supplier registration.

## Files Included

1. **`sample_supplier_application_uganda.txt`** - Uganda-focused supplier application
2. **`sample_supplier_application_international.txt`** - International supplier application
3. **`convert_to_pdf.py`** - Python script to convert text files to PDF
4. **`PDF_SAMPLES_README.md`** - This instruction file

## How to Convert Text Files to PDF

### Option 1: Using the Python Script (Recommended)

1. **Install Python** (if not already installed)
   - Download from: https://www.python.org/downloads/
   - Make sure to check "Add Python to PATH" during installation

2. **Run the conversion script:**
   ```bash
   python convert_to_pdf.py
   ```

3. **The script will:**
   - Install the required `reportlab` library automatically
   - Convert both text files to properly formatted PDFs
   - Create: `sample_supplier_application_uganda.pdf` and `sample_supplier_application_international.pdf`

### Option 2: Manual Conversion

1. **Using Microsoft Word:**
   - Open the `.txt` file in Microsoft Word
   - Go to File → Save As → Choose PDF format
   - Save with appropriate name

2. **Using Google Docs:**
   - Upload the `.txt` file to Google Docs
   - Go to File → Download → PDF Document

3. **Using Online Converters:**
   - Visit: https://www.ilovepdf.com/txt_to_pdf
   - Upload the `.txt` file and convert to PDF

## Validation Guarantee

These sample documents are specifically designed to pass the PDF validation system because they contain:

### ✅ All Required Sections:
1. **Company Information** - Complete business details
2. **Financial Stability** - Revenue, profit, assets, bank references
3. **Business Reputation** - Years in business, clients, certifications
4. **Regulatory Compliance** - Licenses, permits, certifications
5. **Product/Service Summary** - Detailed offerings and capabilities
6. **Declaration** - Legal statement with signature

### ✅ All Required Keywords:
Each section contains the exact keywords the validation system looks for:
- Company: company, business, organization, address, contact, phone, email, website, registration, license
- Financial: financial, revenue, profit, income, assets, liabilities, balance sheet, cash flow, credit, bank, accounting, audit, tax, financial statement
- Reputation: reputation, references, clients, customers, partners, certification, awards, recognition, experience, years, history, track record
- Compliance: compliance, regulatory, license, permit, certification, iso, fda, government, authority, regulation, standard, requirement, approval
- Products: product, service, offering, catalog, inventory, supply, goods, materials, equipment, specialty, category, description
- Declaration: declaration, statement, certify, confirm, truth, accurate, complete, signature, date, authorized, representative, officer

## Testing the Validation System

1. **Start the Java Server:**
   ```bash
   cd java-server
   mvn spring-boot:run
   ```

2. **Register as a Supplier** in the Laravel application

3. **Upload one of the sample PDFs** during registration

4. **Expected Result:** The PDF should pass validation with a score of 80-100%

## Document Differences

### Uganda Sample Features:
- Uses Ugandan Shillings (UGX) for financial data
- References Ugandan institutions (URA, UNBS, UCDA, NARO)
- Includes Ugandan-specific certifications and licenses
- Addresses local agricultural supply chain context
- Uses Ugandan business registration formats

### International Sample Features:
- Uses US Dollars (USD) for financial data
- References international institutions (ISO, FDA, World Bank, UN)
- Includes global certifications (ISO 9001, SOC 2, CMMI)
- Addresses technology and software services
- Uses international business registration formats (DUNS, NAICS)

## Troubleshooting

### If PDF Validation Fails:
1. **Check file format:** Ensure the file is a valid PDF
2. **Check file size:** PDF should be under 10MB
3. **Check text extraction:** PDF should contain extractable text (not just images)
4. **Check content:** Ensure all 6 required sections are present

### If Python Script Fails:
1. **Install Python:** Make sure Python 3.6+ is installed
2. **Check permissions:** Ensure you have write permissions in the directory
3. **Manual installation:** Run `pip install reportlab` manually if needed

## Customization

You can modify the sample documents by:
1. **Editing the `.txt` files** to match your specific business
2. **Adding your company details** while keeping the required sections
3. **Maintaining the keyword density** to ensure validation passes
4. **Converting to PDF** using any of the methods above

## Support

If you encounter issues with the PDF validation system:
1. Check the Java server logs for detailed error messages
2. Verify the PDF contains extractable text
3. Ensure all required sections and keywords are present
4. Test with these guaranteed samples first

---

**Note:** These samples are for testing purposes only. For actual business use, replace with your real company information while maintaining the same structure and keyword coverage. 
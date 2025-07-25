#!/usr/bin/env python3
"""
PDF Converter Script for Supplier Applications
Converts text files to properly formatted PDF documents
"""

import os
from reportlab.lib.pagesizes import letter, A4
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import inch
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_JUSTIFY
from reportlab.lib.colors import black, darkblue, darkgreen

def create_pdf_from_text(text_file, pdf_file):
    """Convert text file to PDF with proper formatting"""
    
    # Read the text file
    with open(text_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Create PDF document
    doc = SimpleDocTemplate(pdf_file, pagesize=A4, 
                          rightMargin=72, leftMargin=72, 
                          topMargin=72, bottomMargin=72)
    
    # Define styles
    styles = getSampleStyleSheet()
    
    # Custom styles
    title_style = ParagraphStyle(
        'CustomTitle',
        parent=styles['Heading1'],
        fontSize=16,
        spaceAfter=30,
        alignment=TA_CENTER,
        textColor=darkblue
    )
    
    section_style = ParagraphStyle(
        'SectionTitle',
        parent=styles['Heading2'],
        fontSize=14,
        spaceAfter=12,
        spaceBefore=20,
        textColor=darkgreen
    )
    
    normal_style = ParagraphStyle(
        'NormalText',
        parent=styles['Normal'],
        fontSize=10,
        spaceAfter=6,
        alignment=TA_JUSTIFY
    )
    
    # Build the story
    story = []
    
    # Split content into sections
    sections = content.split('================================================================================')
    
    for section in sections:
        section = section.strip()
        if not section:
            continue
            
        lines = section.split('\n')
        if not lines:
            continue
            
        # Check if this is a title section
        if 'COVER PAGE' in section or 'SUPPLIER APPLICATION' in section:
            for line in lines:
                line = line.strip()
                if line and not line.startswith('='):
                    if 'SUPPLIER APPLICATION' in line:
                        story.append(Paragraph(line, title_style))
                    else:
                        story.append(Paragraph(line, normal_style))
                    story.append(Spacer(1, 6))
        
        # Check if this is a section with numbered items
        elif any(line.strip().startswith(str(i) + '.') for i in range(1, 7) for line in lines):
            for line in lines:
                line = line.strip()
                if line and not line.startswith('='):
                    if line[0].isdigit() and '. ' in line:
                        # This is a section title
                        story.append(Paragraph(line, section_style))
                    else:
                        story.append(Paragraph(line, normal_style))
                    story.append(Spacer(1, 3))
        
        # Regular content
        else:
            for line in lines:
                line = line.strip()
                if line and not line.startswith('='):
                    story.append(Paragraph(line, normal_style))
                    story.append(Spacer(1, 3))
    
    # Build PDF
    doc.build(story)
    print(f"PDF created successfully: {pdf_file}")

def main():
    """Main function to convert both sample files"""
    
    # Check if reportlab is installed
    try:
        import reportlab
    except ImportError:
        print("ReportLab library not found. Installing...")
        os.system("pip install reportlab")
        import reportlab
    
    # Convert Uganda sample
    if os.path.exists('sample_supplier_application_uganda.txt'):
        create_pdf_from_text('sample_supplier_application_uganda.txt', 
                           'sample_supplier_application_uganda.pdf')
    else:
        print("Uganda sample file not found!")
    
    # Convert International sample
    if os.path.exists('sample_supplier_application_international.txt'):
        create_pdf_from_text('sample_supplier_application_international.txt', 
                           'sample_supplier_application_international.pdf')
    else:
        print("International sample file not found!")
    
    print("\nConversion complete! You can now use these PDF files for testing.")

if __name__ == "__main__":
    main() 
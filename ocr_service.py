import easyocr
import io
from flask import Flask, request, jsonify
from PIL import Image
from pdf2image import convert_from_bytes 

print("Loading EasyOCR models...")
reader = easyocr.Reader(['id', 'en'])
print("EasyOCR models loaded.")

app = Flask(__name__)

@app.route('/ocr', methods=['POST'])
def ocr_request():
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400

    file = request.files['file']
    filename = file.filename
    file_bytes = file.read()
    
    full_text = ""
    
    # Check if the file is a PDF
    if filename.lower().endswith('.pdf'):
        print("Processing PDF file...")
        try:
            # Convert PDF bytes to a list of images
            images = convert_from_bytes(file_bytes)
            # OCR each page (image) and combine the text
            for img in images:
                img_byte_arr = io.BytesIO()
                img.save(img_byte_arr, format='PNG')
                img_byte_arr = img_byte_arr.getvalue()
                
                results = reader.readtext(img_byte_arr)
                extracted_text = [item[1] for item in results]
                full_text += "\n".join(extracted_text) + "\n--- Page Break ---\n"
        except Exception as e:
            return jsonify({'error': f'PDF processing failed: {str(e)}'}), 500
    else:
        # Process as a single image (original logic)
        print("Processing image file...")
        results = reader.readtext(file_bytes)
        extracted_text = [item[1] for item in results]
        full_text = "\n".join(extracted_text)

    print(f"Extracted Text: '{full_text[:100]}...'")
    return jsonify({'text': full_text})

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)
import easyocr
import io
import os
import numpy
import cv2
from flask import Flask, request, jsonify
from PIL import Image
from pdf2image import convert_from_bytes

BLUR_THRESHOLD = 70
CONTRAST_THRESHOLD = 35

THUMBNAIL_DIR = os.path.join('storage', 'app', 'public', 'thumbnails')
os.makedirs(THUMBNAIL_DIR, exist_ok=True)

print("Loading EasyOCR models...")
reader_general = easyocr.Reader(['id', 'en'])
reader_chinese = easyocr.Reader(['ch_tra', 'en'])
print("EasyOCR models loaded.")

app = Flask(__name__)

def validate_image_clarity(image_bytes):
    print("Validating image clarity...")
    nparr = numpy.frombuffer(image_bytes, numpy.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    laplacian_var = cv2.Laplacian(gray, cv2.CV_64F).var()
    print(f"Laplacian Variance (Blur Score): {laplacian_var}")
    if laplacian_var < BLUR_THRESHOLD:
        return (False, f"Image is too blurry. Please upload a clearer photo.")

    contrast = gray.std()
    print(f"Contrast Score: {contrast}")
    if contrast < CONTRAST_THRESHOLD:
        return (False, f"Image has insufficient contrast. Please use a photo with better lighting.")

    return (True, "Image passed validation")

def make_json_serializable(data):
    if isinstance(data, list):
        return [make_json_serializable(item) for item in data]
    if isinstance(data, dict):
        return {key: make_json_serializable(value) for key, value in data.items()}
    if isinstance(data, numpy.int32):
        return int(data)
    if isinstance(data, numpy.ndarray):
        return data.tolist()
    return data

@app.route('/ocr', methods=['POST'])
def ocr_request():
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400

    file = request.files['file']
    filename = file.filename
    file_bytes = file.read()
    
    if not filename.lower().endswith('.pdf'):
        passed, reason = validate_image_clarity(file_bytes)
        if not passed:
            return jsonify({'error': reason}), 400
    
    # Choose which OCR reader to use
    if 'chinese' in filename.lower():
        print("Using Chinese OCR Reader.")
        reader = reader_chinese
    else:
        print("Using General OCR Reader.")
        reader = reader_general
    
    full_text_list = []
    word_data = []
    thumbnail_path = None

    try:
        if filename.lower().endswith('.pdf'):
            print("Processing PDF file...")
            first_page_image = convert_from_bytes(file_bytes, first_page=1, last_page=1)[0]
            
            base_filename = os.path.splitext(os.path.basename(filename))[0]
            thumbnail_filename = f"{base_filename}_thumb.jpeg"
            thumbnail_save_path = os.path.join(THUMBNAIL_DIR, thumbnail_filename)
            first_page_image.save(thumbnail_save_path, 'JPEG')
            thumbnail_path = os.path.join('thumbnails', thumbnail_filename)
            
            images = convert_from_bytes(file_bytes)
            for page_num, img in enumerate(images):
                img_byte_arr = io.BytesIO()
                img.save(img_byte_arr, format='PNG')
                img_bytes_page = img_byte_arr.getvalue()
                results = reader.readtext(img_bytes_page, detail=1)
                for (bbox, text, prob) in results:
                    full_text_list.append(text)
                    word_data.append({ 'text': text, 'box': bbox, 'page': page_num + 1 })
        else:
            print("Processing image file...")
            results = reader.readtext(file_bytes, detail=1)
            for (bbox, text, prob) in results:
                full_text_list.append(text)
                word_data.append({ 'text': text, 'box': bbox, 'page': 1 })
        
        full_text = "\n".join(full_text_list)
        cleaned_word_data = make_json_serializable(word_data)
        
        return jsonify({
            'text': full_text, 
            'word_data': cleaned_word_data,
            'thumbnail_path': thumbnail_path
        })

    except Exception as e:
        return jsonify({'error': f'Processing failed: {str(e)}'}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)
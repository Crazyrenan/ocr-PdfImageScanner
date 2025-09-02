# ocr_service.py
import easyocr
import io
import numpy 
from flask import Flask, request, jsonify
from PIL import Image
from pdf2image import convert_from_bytes

print("Loading EasyOCR models...")
reader = easyocr.Reader(['id', 'en'])
print("EasyOCR models loaded.")

app = Flask(__name__)


def make_json_serializable(data):
    if isinstance(data, list):
        return [make_json_serializable(item) for item in data]
    if isinstance(data, dict):
        return {key: make_json_serializable(value) for key, value in data.items()}
    if isinstance(data, numpy.int32):
        return int(data) 
    if isinstance(data, numpy.ndarray):
        return data.tolist() 

@app.route('/ocr', methods=['POST'])
def ocr_request():
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400

    file = request.files['file']
    filename = file.filename
    file_bytes = file.read()
    
    full_text_list = []
    word_data = []
    page_counter = 0

    try:
        if filename.lower().endswith('.pdf'):
            images = convert_from_bytes(file_bytes)
            for page_num, img in enumerate(images):
                page_counter = page_num + 1
                img_byte_arr = io.BytesIO()
                img.save(img_byte_arr, format='PNG')
                img_byte_arr = img_byte_arr.getvalue()
                
                results = reader.readtext(img_byte_arr, detail=1)
                for (bbox, text, prob) in results:
                    full_text_list.append(text)
                    word_data.append({ 'text': text, 'box': bbox, 'page': page_counter })
        else:
            results = reader.readtext(file_bytes, detail=1)
            for (bbox, text, prob) in results:
                full_text_list.append(text)
                word_data.append({ 'text': text, 'box': bbox, 'page': 1 })
        
        full_text = "\n".join(full_text_list)
        

        cleaned_word_data = make_json_serializable(word_data)
        
        return jsonify({'text': full_text, 'word_data': cleaned_word_data})

    except Exception as e:
        return jsonify({'error': f'Processing failed: {str(e)}'}), 500

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)
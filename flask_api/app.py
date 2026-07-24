from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoTokenizer
import numpy as np
import onnxruntime as ort
import pickle
import json
import random
from pathlib import Path

app = Flask(__name__)
CORS(app)

# Path model dibuat berdasarkan lokasi file ini agar API dapat dijalankan
# dari folder proyek mana pun.
BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "model_chatbot_onnx"
ONNX_MODEL_PATH = MODEL_PATH / "model_int8.onnx"
LABEL_ENCODER_PATH = BASE_DIR / "label_encoder.pkl"
INTENTS_PATH = BASE_DIR / "intents.json"

# Load tokenizer dan model ONNX INT8.
tokenizer = AutoTokenizer.from_pretrained(MODEL_PATH)
session = ort.InferenceSession(
    str(ONNX_MODEL_PATH),
    providers=["CPUExecutionProvider"]
)
input_names = {item.name for item in session.get_inputs()}

# Load label encoder
with open(LABEL_ENCODER_PATH, "rb") as file:
    label_encoder = pickle.load(file)

# Load intents
with open(INTENTS_PATH, "r", encoding="utf-8") as file:
    intents = json.load(file)


def softmax(logits):
    logits = logits - np.max(logits, axis=1, keepdims=True)
    probabilities = np.exp(logits)
    return probabilities / np.sum(probabilities, axis=1, keepdims=True)

def get_response(user_input, threshold=0.55):
    inputs = tokenizer(
        user_input,
        return_tensors="np",
        truncation=True,
        padding=True,
        max_length=64
    )

    inputs = {
        key: value
        for key, value in inputs.items()
        if key in input_names
    }
    logits = session.run(None, inputs)[0]
    probs = softmax(logits)
    predicted_id = int(np.argmax(probs, axis=1)[0])
    confidence_score = float(probs[0, predicted_id])
    predicted_label = label_encoder.inverse_transform([predicted_id])[0]

    if confidence_score < threshold:
        return {
            "intent": "unknown",
            "confidence": confidence_score,
            "response": "Maaf, saya belum memahami pertanyaan tersebut. Chatbot ini hanya membantu informasi seputar layanan klinik gigi dan konsultasi awal"
        }

    for intent in intents["intents"]:
        if intent["tag"] == predicted_label:
            return {
                "intent": predicted_label,
                "confidence": confidence_score,
                "response": random.choice(intent["responses"])
            }

    return {
        "intent": "error",
        "confidence": confidence_score,
        "response": "Maaf, terjadi kesalahan pada sistem chatbot."
    }

@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "message": "API Chatbot Klinik Gigi aktif"
    })

@app.route("/chatbot", methods=["POST"])
def chatbot():
    data = request.get_json(silent=True)

    if not data or "message" not in data:
        return jsonify({
            "error": "Pesan tidak ditemukan"
        }), 400

    user_message = data["message"]
    if not isinstance(user_message, str) or not user_message.strip():
        return jsonify({
            "error": "Pesan harus berupa teks dan tidak boleh kosong"
        }), 400

    user_message = user_message.strip()
    result = get_response(user_message)

    return jsonify(result)

if __name__ == "__main__":
    app.run(debug=False, port=5000)

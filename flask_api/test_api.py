import requests

url = "http://127.0.0.1:5001/chatbot"

data = {
    "message": "halo kak mau scaling gigi"
}

response = requests.post(url, json=data)

print(response.json())
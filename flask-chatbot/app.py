from flask import Flask, request, jsonify, session
from flask_cors import CORS
from dialog_manager import manage_dialog

app = Flask(__name__)
CORS(app)
app.secret_key = "votre_cle_secrete_super_securisee"

@app.route('/chatbot', methods=['POST'])
def chatbot():
    data = request.get_json()
    message = data.get("message", "")
    response = manage_dialog(session, message)
    return jsonify({"reply": response})

if __name__ == "__main__":
    app.run(debug=True)

# Chatbot FlixBus - Multi-Turn (Français)

Ce projet est un chatbot en Python/Flask capable de :

- Gérer des dialogues en plusieurs étapes
- Identifier les villes même avec des fautes de frappe
- Comprendre des dates comme "demain", "le 25 mai", etc.

## Installation

```bash
pip install -r requirements.txt
```

## Lancement

```bash
python app.py
```

Accédez à `http://localhost:5000/chatbot` via POST avec JSON :

```json
{
  "message": "je veux réserver un ticket"
}
```

Le chatbot vous guidera étape par étape.

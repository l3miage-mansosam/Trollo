import difflib
import re
from datetime import datetime, timedelta

cities = [
    "paris", "lyon", "marseille", "grenoble", "valence", "strasbourg",
    "lille", "bordeaux", "nantes", "nice", "dijon", "rouen", "angers", "reims"
]

sample_routes = {
    ("paris", "marseille"): ["08h00", "13h00", "20h00"],
    ("valence", "strasbourg"): ["07h15", "18h00"],
    ("lyon", "paris"): ["09h00", "14h00"],
    ("paris", "grenoble"): ["07h30", "12h00", "17h45"],
    ("paris", "nice"): ["06h45", "16h20"],
    ("marseille", "lyon"): ["10h00", "15h30"]
}

day_keywords = {
    "aujourd’hui": 0,
    "demain": 1,
    "après-demain": 2,
    "lundi": 0,
    "mardi": 1,
    "mercredi": 2,
    "jeudi": 3,
    "vendredi": 4,
    "samedi": 5,
    "dimanche": 6,
}

def fuzzy_city(word):
    word = word.lower()
    closest = difflib.get_close_matches(word, cities, n=1, cutoff=0.8)
    return closest[0] if closest else None

def extract_date_from_message(message):
    message = message.lower()
    today = datetime.today()

    match = re.search(r"le\s+(\d{1,2})\s+(janvier|février|mars|avril|mai|juin|juillet|août|septembre|octobre|novembre|décembre)", message)
    if match:
        day = int(match.group(1))
        month_str = match.group(2)
        month_map = {
            "janvier": 1, "février": 2, "mars": 3, "avril": 4,
            "mai": 5, "juin": 6, "juillet": 7, "août": 8,
            "septembre": 9, "octobre": 10, "novembre": 11, "décembre": 12
        }
        month = month_map.get(month_str)
        try:
            return datetime(today.year, month, day).strftime("%Y-%m-%d")
        except:
            return None

    for word, offset in day_keywords.items():
        if word in message:
            if word in ["lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche"]:
                target_weekday = offset
                days_ahead = (target_weekday - today.weekday() + 7) % 7
                if days_ahead == 0:
                    days_ahead = 7
                target_date = today + timedelta(days=days_ahead)
            else:
                target_date = today + timedelta(days=offset)
            return target_date.strftime("%Y-%m-%d")

    return None

def manage_dialog(session, user_input):
    user_input = user_input.lower()

    if "state" not in session:
        session["state"] = "ask_from"
        return "Bonjour ! Dites-moi votre ville de départ."

    if session["state"] == "ask_from":
        city = fuzzy_city(user_input)
        if city:
            session["from_city"] = city
            session["state"] = "ask_to"
            return f"Très bien. Quelle est votre ville d’arrivée ?"
        return "Je n'ai pas reconnu la ville de départ. Veuillez réessayer."

    elif session["state"] == "ask_to":
        city = fuzzy_city(user_input)
        if city:
            session["to_city"] = city
            session["state"] = "ask_date"
            return f"Parfait. Pour quel jour souhaitez-vous voyager ?"
        return "Je n'ai pas reconnu la ville d’arrivée. Veuillez réessayer."

    elif session["state"] == "ask_date":
        date_str = extract_date_from_message(user_input)
        if date_str:
            session["date"] = date_str
            from_city = session["from_city"]
            to_city = session["to_city"]
            route_key = (from_city, to_city)
            session.clear()
            if route_key in sample_routes:
                horaires = sample_routes[route_key]
                horaires_txt = "\n- " + "\n- ".join(horaires)
                return f"Voici les horaires de {from_city.title()} à {to_city.title()} pour le {date_str} :{horaires_txt}"
            else:
                return f"Désolé, nous n'avons pas trouvé de trajets entre {from_city.title()} et {to_city.title()}."
        return "Je n’ai pas compris la date. Essayez avec 'demain', 'samedi', ou 'le 25 mai'."

    return "Je n’ai pas bien compris. Recommençons. Quelle est votre ville de départ ?"

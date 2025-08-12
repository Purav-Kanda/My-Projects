from flask import Flask, render_template, request
import json
from nlp_utils import preprocess

app = Flask(__name__)

with open("intents.json") as file:
    data = json.load(file)

def find_intent(user_input):
    processed = preprocess(user_input)
    for intent in data["intents"]:
        for pattern in intent["patterns"]:
            pattern_words = preprocess(pattern)
            if all(word in processed for word in pattern_words):
                return intent
    return None

@app.route("/", methods=["GET", "POST"])
def index():
    response = ""
    if request.method == "POST":
        user_input = request.form["message"]
        intent = find_intent(user_input)
        if intent:
            response = intent["responses"][0]
        else:
            response = "Sorry, I didn’t understand that."
    return render_template("index.html", response=response)

if __name__ == "__main__":
    app.run(debug=True)


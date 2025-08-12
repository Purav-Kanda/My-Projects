import json
from nlp_utils import preprocess

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

print("Campus Helper Chatbot! Type 'quit' to exit.")

while True:
    user_input = input("You: ")
    if user_input.lower() == "quit":
        print("Bot: Bye! Have a great day.")
        break

    intent = find_intent(user_input)
    if intent:
        response = intent["responses"][0]
        print("Bot:", response)
    else:
        print("Bot: Sorry, I didn’t understand that. Try asking something else.")

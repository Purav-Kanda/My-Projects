from nltk.stem import WordNetLemmatizer
from nltk.tokenize import wordpunct_tokenize
import string

lemmatizer = WordNetLemmatizer()

def preprocess(sentence):
    # Use wordpunct_tokenize instead of word_tokenize
    tokens = wordpunct_tokenize(sentence.lower())
    tokens = [lemmatizer.lemmatize(word) for word in tokens if word not in string.punctuation]
    return tokens

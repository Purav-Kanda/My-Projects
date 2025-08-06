import base64
from cryptography.fernet import Fernet
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC
import getpass
import json
import os

# File to store passwords
PASSWORDS_FILE = "passwords.enc"

def generate_key(password, salt):
    """Generate encryption key from password"""
    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=32,
        salt=salt,
        iterations=100000,
    )
    return base64.urlsafe_b64encode(kdf.derive(password.encode()))

def encrypt_data(data, key):
    """Encrypt data using Fernet"""
    f = Fernet(key)
    return f.encrypt(data.encode())

def decrypt_data(encrypted_data, key):
    """Decrypt data using Fernet"""
    f = Fernet(key)
    return f.decrypt(encrypted_data).decode()

def load_passwords(key):
    """Load passwords from file"""
    if not os.path.exists(PASSWORDS_FILE):
        return {}
    
    with open(PASSWORDS_FILE, "rb") as f:
        encrypted = f.read()
    
    decrypted = decrypt_data(encrypted, key)
    return json.loads(decrypted)

def save_passwords(passwords, key):
    """Save passwords to file"""
    encrypted = encrypt_data(json.dumps(passwords), key)
    with open(PASSWORDS_FILE, "wb") as f:
        f.write(encrypted)

def main():
    print("=== Simple Password Manager ===")
    
    # Set up master password
    if not os.path.exists("salt.bin"):
        # First run - create salt and master password
        salt = os.urandom(16)
        with open("salt.bin", "wb") as f:
            f.write(salt)
        
        master_password = getpass.getpass("Create master password: ")
        confirm = getpass.getpass("Confirm master password: ")
        if master_password != confirm:
            print("Passwords don't match!")
            return
    else:
        # Existing user - load salt
        with open("salt.bin", "rb") as f:
            salt = f.read()
        master_password = getpass.getpass("Enter master password: ")
    
    # Generate encryption key
    key = generate_key(master_password, salt)
    
    # Load existing passwords
    passwords = load_passwords(key)
    
    while True:
        print("\n1. Add password")
        print("2. View passwords")
        print("3. Exit")
        choice = input("Choose an option (1-3): ")
        
        if choice == "1":
            # Add new password
            service = input("Enter service name (e.g. 'Gmail'): ")
            username = input("Enter username: ")
            password = getpass.getpass("Enter password: ")
            
            passwords[service] = {
                "username": username,
                "password": password
            }
            save_passwords(passwords, key)
            print("Password saved!")
        
        elif choice == "2":
            # View passwords
            if not passwords:
                print("No passwords stored yet!")
                continue
                
            print("\nStored passwords:")
            for service, details in passwords.items():
                print(f"\nService: {service}")
                print(f"Username: {details['username']}")
                print(f"Password: {details['password']}")
        
        elif choice == "3":
            print("Goodbye!")
            break
        
        else:
            print("Invalid choice. Please try again.")

if __name__ == "__main__":
    main()
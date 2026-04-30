# Secure Password Manager CLI 🔐

![Python](https://img.shields.io/badge/Python-3.8+-blue)
![Cryptography](https://img.shields.io/badge/Cryptography-Fernet-brightgreen)
![License](https://img.shields.io/badge/License-MIT-yellow)

A secure command-line password manager with military-grade encryption, built for developers who value privacy.

![Demo GIF](demo.gif) <!-- Replace with actual demo -->

## 🌟 Features
- **Zero-Knowledge Security**
  - AES-256-GCM encryption via Fernet
  - PBKDF2 key derivation (100,000 iterations)
  - Unique cryptographic salt per installation
- **Intuitive CLI**
  - Add/View/Delete credentials
  - Master password protection
  - Secure password generation
- **Portable Design**
  - Single-file implementation
  - Cross-platform (Windows/macOS/Linux)

## 🚀 Quick Start

### Prerequisites
- Python 3.8+
- pip package manager

### Installation
```bash
git clone https://github.com/Purav-Kanda/My-Projects.git
cd My-Projects/PASSWORD\ MANAGER\ CLI/
pip install cryptography

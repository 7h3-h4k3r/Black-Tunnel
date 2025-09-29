
# Black Tunnel v0.1

A lightweight **VPN & API-based management system** built around **WireGuard**, providing authentication, interface, and peer management through REST APIs.  
Designed for developers, sysadmins, and security researchers who want programmatic control of VPN infrastructure.

---

## 🚀 Features
- **User Authentication**
  - Signup, login, token refresh, and current user info.
- **WireGuard Interface Management**
  - Create, bring up/down, and delete interfaces.
- **Peer Management**
  - Add, remove, and list peers securely.
- **RESTful API**
  - Centralized entry point for backend operations.
- **CLI & Python Toolkit**
  - Python `wgctl` package for WireGuard control.
- **CIDR-based IP Management**
  - Automatic IP allocation for peers.
- **Low-level Utilities**
  - C-based IP calculation for performance-critical tasks.

---

## 📂 Project Structure

```

Black-Tunnel/
├── api/                      # REST API endpoints
│   ├── apis/
│   │   ├── auth/             # Authentication APIs
│   │   │   ├── current.php
│   │   │   ├── login.php
│   │   │   ├── refresh.php
│   │   │   └── signup.php
│   │   ├── interface/        # VPN interface control
│   │   │   ├── add.php
│   │   │   ├── del.php
│   │   │   ├── down.php
│   │   │   └── up.php
│   │   └── wg/               # Peer control
│   │       ├── addpeer.php
│   │       ├── delpeer.php
│   │       ├── getpeer.php
│   │       └── getpeers.php
│   ├── lib/                  # Core logic
│   │   ├── Authentication/
│   │   ├── Credentials/
│   │   ├── Database/
│   │   └── wireGuard/
│   ├── REST.api.php
│   └── index.php
│
├── cidr/                     # CIDR-based IP pool files
│   └── e.g., 10_0_0_1-24.txt
│
├── drivers/                  # Low-level IP calculation tools
│   ├── calculate-ip.c
│   ├── calculate-ip.o
│   └── Makefile
│
├── wgctl/                     # Python-based WireGuard controller
│   ├── main.py
│   ├── up.py
│   ├── down.py
│   ├── wgctl.py
│   ├── getconfig.py
│   └── removeInterface.py
│
├── .htaccess
├── composer.json
├── composer.lock
└── testing.php

````

---

## 🔑 Core Modules Overview

### Authentication (`api/apis/auth/`)
| File          | Description                          |
|---------------|--------------------------------------|
| `signup.php`  | Register new users (validates input, stores hashed passwords). |
| `login.php`   | Authenticates credentials and issues token/session. |
| `refresh.php` | Renews expired tokens for seamless login. |
| `current.php` | Returns current logged-in user info. |

---

### Interface Management (`api/apis/interface/`)
| File         | Purpose                               |
|--------------|---------------------------------------|
| `add.php`    | Create a new WireGuard interface.     |
| `up.php`     | Activate the interface.               |
| `down.php`   | Temporarily deactivate it.            |
| `del.php`    | Remove the interface completely.      |

---

### Peer Management (`api/apis/wg/`)
| File            | Purpose                              |
|-----------------|--------------------------------------|
| `addpeer.php`   | Add a peer to a WireGuard interface. |
| `delpeer.php`   | Remove an existing peer.            |
| `getpeer.php`   | Retrieve details of a specific peer.|
| `getpeers.php`  | List all peers for an interface.    |

---

### Libraries (`api/lib/`)
- **Authentication/**
  - `Auth.class.php` – Core login/session/JWT handling.
  - `OAuth.class.php` – Third-party login integration.
- **Credentials/**
  - `Signup.class.php` – Signup processing.
  - `User.class.php` – User data model & helpers.
- **Database/**
  - `Database.class.php` – Secure DB connection & query executor.
- **wireGuard/**
  - `interface.class.php` – Handles interface creation/up/down.
  - `wireguard.class.php` – Peer management and sync.

---

### CIDR Files (`cidr/`)
- Provide IP pools for different subnets (e.g., `10_0_0_1-24.txt`).
- Used to auto-assign IPs to new peers.

---

### Drivers (`drivers/`)
- `calculate-ip.c` – Computes IP ranges from CIDR.
- `Makefile` – Automates compilation of tools.

---

### Python CLI (`wgctl/`)
| Script              | Description                          |
|---------------------|--------------------------------------|
| `main.py`           | CLI entrypoint for interface/peer ops.|
| `up.py`             | Bring an interface up.               |
| `down.py`           | Bring an interface down.             |
| `removeInterface.py`| Remove interface from system & DB.   |
| `wgctl.py`          | Core controller functions.          |
| `getconfig.py`      | Retrieve current interface & peer info.|

---

## ⚙️ Installation

### Requirements
- PHP 8.x+
- Composer
- Python 3.8+
- WireGuard installed on host
- GCC (for building IP utilities)

### Steps
```bash
# Clone the repository
git clone git@github.com:7h3-h4k3r/Black-Tunnel.git
cd Black-Tunnel

# Install PHP dependencies
composer install

# Build C utilities
cd drivers
make

# (Optional) Test Python CLI
cd ../wgctl
python3 main.py --help
````

---

## 🌐 API Usage Examples

### 1. Signup

```http
POST /api/auth/signup.php
{
  "username": "alice",
  "email": "alice@example.com",
  "password": "securePass123"
}
```

### 2. Add a Peer

```http
POST /api/wg/addpeer.php
{
  "interface": "wg0",
  "public_key": "abc123...",
  "allowed_ips": "10.0.0.2/32"
}
```

### 3. Bring Interface Up

```http
POST /api/interface/up.php
{
  "interface": "wg0"
}
```

---

## 🛡️ Security Notes

* All sensitive credentials are hashed before storage.
* Use HTTPS in production.
* Rotate keys and tokens periodically.
* Ensure proper firewall rules when exposing interfaces.

---

## 📜 License

MIT License – see [LICENSE](https://github.com/7h3-h4k3r/Black-Tunnel?tab=MIT-1-ov-file#) file for details.

---

## 👤 Author

**Black Tunnel v0.1**
Developed by [7h3-h4k3r](https://github.com/7h3-h4k3r)

```
EAT , CODE , SLEEP
```

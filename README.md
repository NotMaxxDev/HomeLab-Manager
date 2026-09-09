# 🛠️ HomeLab Manager

**HomeLab Manager** ist eine zentrale Verwaltungsoberfläche für dein Homelab. Es hilft dir beim Verwalten von Docker-Containern, Projekten, Monitoring-Metriken, Notizen, LLM-Integrationen und MCP-Tokens.

---

## ✨ Features

- **📊 Dashboard & Monitoring**: Überblick über Server-Metriken (CPU, RAM, Disks) und Docker-Container.
- **🐳 Docker Container Management**: Verwalte, starte und stoppe deine Docker-Container direkt im Dashboard.
- **📁 Projektverwaltung**: Gruppiere Container und Dienste in übersichtliche Projekte.
- **📝 Notizen & Dokumentation**: Halte Server-Konfigurationen und Snippets mit Versionsverlauf fest.
- **🤖 LLM Agent Integration**: Unterstützung für OpenAI, Anthropic & MCP (Model Context Protocol).
- **🌐 Mehrsprachig & Themes**: Deutsch & Englisch Unterstützung inkl. Dark Mode.

---

## 🚀 Installation

Du kannst **HomeLab Manager** schnell und einfach über Docker (Docker Compose oder Laravel Sail) oder direkt lokal installieren.

---

### Option A: Installation via Docker Compose (Empfohlen)

#### 1. Repository klonen
```bash
git clone https://github.com/NotMaxxDev/HomeLab-Manager.git
cd HomeLab-Manager
```

#### 2. Umgebungsdatei erstellen
```bash
cp .env.example .env
```

#### 3. Docker Compose starten
Erstelle eine `docker-compose.yml` (falls noch nicht vorhanden) oder verwende das folgende Standard-Setup:

```yaml
version: '3.8'

services:
  app:
    image: php:8.4-cli
    container_name: homelab_manager
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - .:/var/www/html
      - /var/run/docker.sock:/var/run/docker.sock
    ports:
      - "4000:8000"
    command: >
      sh -c "composer install --no-dev --optimize-autoloader &&
            php artisan key:generate --force &&
            php artisan migrate --force &&
            php artisan serve --host=0.0.0.0 --port=8000"
```

Starte den Container:
```bash
docker compose up -d
```

Öffne anschließend im Browser: **`http://localhost:4000`** (oder IP deines Servers).

---

### Option B: Installation via Laravel Sail (Für Entwickler)

Wenn du bereits Docker auf deinem Rechner installiert hast, kannst du Laravel Sail nutzen:

#### 1. Repository klonen & `.env` kopieren
```bash
git clone https://github.com/NotMaxxDev/HomeLab-Manager.git
cd HomeLab-Manager
cp .env.example .env
```

#### 2. Abhängigkeiten über Docker installieren
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-app:latest \
    composer install --ignore-platform-reqs
```

#### 3. Sail starten & Setup ausführen
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Öffne anschließend **`http://localhost`**.

---

### Option C: Manuelle lokale Installation (ohne Docker)

#### Voraussetzungen
- PHP >= 8.3 (mit Extensions: `pdo`, `sqlite3`, `mbstring`, `openssl`, `curl`)
- Composer
- Node.js & NPM

#### Schritte:
```bash
# 1. Klonen
git clone https://github.com/NotMaxxDev/HomeLab-Manager.git
cd HomeLab-Manager

# 2. Setup ausführen (Installiert Composer, NPM, Keys & Migrationen)
composer run-script setup

# 3. Server auf Port 4000 starten
php artisan serve --port=4000
```

Öffne **`http://127.0.0.1:4000`** in deinem Browser.

---

## ⚙️ Wichtige Konfigurationen (`.env`)

- **Docker Integration**: Damit HomeLab Manager lokale Docker-Container verwalten kann, muss die App Zugriff auf den Docker-Socket haben (bei Docker Compose durch das Volume `- /var/run/docker.sock:/var/run/docker.sock`).
- **Datenbank**: Standardmäßig verwendet die Anwendung SQLite (`database/database.sqlite`).

---

## 📜 Lizenz

Dieses Projekt steht unter der [MIT-Lizenz](LICENSE).

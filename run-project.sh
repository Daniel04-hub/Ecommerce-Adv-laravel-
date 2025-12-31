#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   chmod +x run-project.sh
#   ./run-project.sh
#
# What this script does (in order):
#  1) Ensures dependencies are installed (Composer + NPM)
#  2) Ensures a local .env exists and APP_KEY is set (no overwrite)
#  3) Clears caches and runs migrations (idempotent)
#  4) Starts: Reverb (broadcasting), Laravel HTTP server, Vite dev server
#  5) Starts 3 Redis queue workers: payment_queue, inventory_queue, shipping_queue
#
# Constraints:
#  - No Docker
#  - Safe to run multiple times (won't overwrite .env; migrations are incremental)
#  - Does NOT change application logic

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$REPO_ROOT"

# -------------------------
# Helpers
# -------------------------
log() { printf "[%s] %s\n" "$(date +"%H:%M:%S")" "$*"; }

require_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Missing required command: $1" >&2
    exit 1
  fi
}

port_in_use() {
  # Returns 0 if port is in use, 1 otherwise.
  # Uses ss (preferred) or netstat if available.
  local port="$1"
  if command -v ss >/dev/null 2>&1; then
    ss -ltn 2>/dev/null | awk '{print $4}' | grep -Eq "(^|:)${port}$"
  elif command -v netstat >/dev/null 2>&1; then
    netstat -ltn 2>/dev/null | awk '{print $4}' | grep -Eq "(^|:)${port}$"
  else
    return 1
  fi
}

ensure_port_free() {
  local port="$1"
  local name="$2"
  if port_in_use "$port"; then
    echo "$name port ${port} is already in use. Stop the service using it and re-run." >&2
    exit 1
  fi
}

# Kill any background processes started by this script.
PIDS=()
cleanup() {
  log "Stopping processes..."
  for pid in "${PIDS[@]:-}"; do
    if kill -0 "$pid" >/dev/null 2>&1; then
      kill "$pid" >/dev/null 2>&1 || true
    fi
  done
  wait || true
}
trap cleanup INT TERM EXIT

# -------------------------
# Prerequisites
# -------------------------
require_cmd php
require_cmd composer
require_cmd npm

# Redis is required for queues and (optionally) Reverb scaling.
if command -v redis-cli >/dev/null 2>&1; then
  if ! redis-cli ping >/dev/null 2>&1; then
    log "WARNING: redis-cli ping failed. Ensure Redis is running (queues will not work)."
  fi
else
  log "WARNING: redis-cli not found. Ensure Redis is installed and running."
fi

# -------------------------
# Install dependencies (safe to re-run)
# -------------------------
log "Installing PHP dependencies (Composer)..."
composer install --no-interaction --prefer-dist

log "Installing JS dependencies (NPM)..."
if [[ -f package-lock.json ]]; then
  npm ci
else
  npm install
fi

# -------------------------
# Environment (.env) + APP_KEY (no overwrite)
# -------------------------
if [[ ! -f .env ]]; then
  log "Creating .env from .env.example (no overwrite)..."
  cp -n .env.example .env
fi

if ! grep -Eq '^APP_KEY=.+$' .env; then
  log "Generating APP_KEY (updates .env)..."
  php artisan key:generate --no-interaction
fi

# Mailtrap visibility depends on your .env MAIL_* settings.
# Ensure MAIL_MAILER=smtp and MAIL_HOST/MAIL_USERNAME/MAIL_PASSWORD match your Mailtrap inbox.
if grep -Ei "^MAIL_HOST=.*mailtrap|^MAILTRAP_" .env >/dev/null 2>&1; then
  log "Mailtrap settings detected in .env. Emails should land in Mailtrap when triggered."
else
  log "WARNING: Mailtrap settings not detected in .env. Configure MAIL_* for Mailtrap to view emails."
fi

# -------------------------
# App prep (safe to re-run)
# -------------------------
log "Clearing cached config/routes/views (safe)..."
php artisan optimize:clear

log "Ensuring storage symlink exists (safe)..."
php artisan storage:link || true

log "Running database migrations (idempotent)..."
php artisan migrate --no-interaction

# -------------------------
# Start services
# -------------------------
LARAVEL_HOST="127.0.0.1"
LARAVEL_PORT="8000"
VITE_HOST="127.0.0.1"
VITE_PORT="5173"
REVERB_HOST="127.0.0.1"
REVERB_PORT="8080"

ensure_port_free "$LARAVEL_PORT" "Laravel HTTP server"
ensure_port_free "$VITE_PORT" "Vite dev server"
ensure_port_free "$REVERB_PORT" "Reverb broadcasting"

log "Starting Reverb (broadcasting) on ${REVERB_HOST}:${REVERB_PORT}..."
# Reverb command is already present in this repo. If this is your first run ever, you may need:
#   php artisan reverb:install
php artisan reverb:start --host="${REVERB_HOST}" --port="${REVERB_PORT}" &
PIDS+=("$!")

log "Starting Laravel HTTP server on http://${LARAVEL_HOST}:${LARAVEL_PORT} ..."
php artisan serve --host="${LARAVEL_HOST}" --port="${LARAVEL_PORT}" &
PIDS+=("$!")

log "Starting Vite dev server on http://${VITE_HOST}:${VITE_PORT} ..."
npm run dev -- --host "${VITE_HOST}" --port "${VITE_PORT}" &
PIDS+=("$!")

log "Starting Redis queue worker: payment_queue ..."
php artisan queue:work redis --queue=payment_queue --sleep=1 --tries=3 --timeout=90 &
PIDS+=("$!")

log "Starting Redis queue worker: inventory_queue ..."
php artisan queue:work redis --queue=inventory_queue --sleep=1 --tries=3 --timeout=90 &
PIDS+=("$!")

log "Starting Redis queue worker: shipping_queue ..."
php artisan queue:work redis --queue=shipping_queue --sleep=1 --tries=3 --timeout=90 &
PIDS+=("$!")

log "All processes started."
log "Open the app: http://${LARAVEL_HOST}:${LARAVEL_PORT}"
log "Vite dev server: http://${VITE_HOST}:${VITE_PORT}"
log "Reverb (WS): ws://${REVERB_HOST}:${REVERB_PORT}"
log "Press Ctrl+C to stop everything."

wait


#To run this projecto copy and paste it 
#___________________________________________|


# chmod +x run-project.sh
# ./run-project.sh


#Credentials (password is the password)
#------------------------------------------------------|
#vendor@gmail.com                                      |
#customer@example.com                                  |
#admin@example.com("google auth(socialite              |
#------------------------------------------------------|

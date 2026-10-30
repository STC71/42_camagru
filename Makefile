# ============================================================================
#  📸 CAMAGRU — Makefile
#  42 Málaga · Outer Core · Docker / PHP / MySQL
#  Uso: make  |  make help  |  make all
# ============================================================================

MAKEFLAGS += --no-print-directory

# Detectar docker compose v1/v2
DOCKER_COMPOSE := $(shell if docker compose version >/dev/null 2>&1; then echo "docker compose"; \
	elif docker-compose version >/dev/null 2>&1; then echo "docker-compose"; \
	else echo "docker-compose"; fi)

APP_URL ?= http://localhost:8080

# ==================== COLORES =================================================
NEGRITA  := \033[1m
RESET    := \033[0m
CIAN     := \033[36m
VERDE    := \033[32m
AMARILLO := \033[33m
ROJO     := \033[31m
AZUL     := \033[34m
MAGENTA  := \033[35m

# ==================== PHONY ===================================================
.PHONY: help header build up down restart logs \
	clean fclean purge-env install setup env \
	test test-full test-eval \
	shell db status ps \
	ext permissions urls open \
	all re rebuild

.DEFAULT_GOAL := help

# ==================== UI ======================================================

header:
	@echo "$(CIAN)$(NEGRITA)╔══════════════════════════════════════════════════════════════════════╗$(RESET)"
	@echo "$(CIAN)$(NEGRITA)║  $(VERDE)📸  CAMAGRU · Docker / PHP / MySQL · 42 Málaga$(CIAN)                   ║$(RESET)"
	@echo "$(CIAN)$(NEGRITA)╚══════════════════════════════════════════════════════════════════════╝$(RESET)"
	@echo ""

help: ## Muestra este menú de ayuda
	@echo "$(AZUL)$(NEGRITA)================================================================================$(RESET)"
	@echo "$(AZUL)$(NEGRITA)                         🛡️  CAMAGRU — MAKE HELP 🛡️$(RESET)"
	@echo "$(AZUL)$(NEGRITA)================================================================================$(RESET)"
	@echo ""
	@echo "$(VERDE)Compose:$(RESET) $(DOCKER_COMPOSE)    $(VERDE)APP_URL:$(RESET) $(APP_URL)"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "}; /^[a-zA-Z0-9_-]+:.*?## / {printf "  $(CIAN)%-14s$(RESET) %s\n", $$1, $$2}' $(MAKEFILE_LIST)
	@echo ""
	@echo "$(AMARILLO)Flujo típico:$(RESET)  make setup → make build → make up → make test-full"
	@echo "$(AMARILLO)Todo en uno:$(RESET)   make all"
	@echo ""

# ==================== ENTORNO =================================================

setup: ## Crea .env si falta (env_setup.sh o copia .env.example)
	@echo "$(AMARILLO)🛠️  Configurando entorno...$(RESET)"
	@if [ -f .env ]; then \
		echo "$(VERDE)  ✓ .env ya existe$(RESET)"; \
	elif [ -x ./env_setup.sh ]; then \
		./env_setup.sh; \
	elif [ -f ./env_setup.sh ]; then \
		chmod +x ./env_setup.sh; ./env_setup.sh; \
	elif [ -f .env.example ]; then \
		cp .env.example .env; \
		chmod 644 .env; \
		echo "$(VERDE)  ✓ .env creado desde .env.example — edita MAIL_* y secretos$(RESET)"; \
	else \
		echo "$(ROJO)  ✗ No hay env_setup.sh ni .env.example$(RESET)"; \
		exit 1; \
	fi

env: setup ## Alias de setup

# ==================== DOCKER ==================================================

build: header ## Construye las imágenes (web + db)
	@echo "$(MAGENTA)🏗️  docker compose build...$(RESET)"
	@$(DOCKER_COMPOSE) build

up: ## Arranca los contenedores en segundo plano
	@echo "$(VERDE)🚀  Levantando servicios...$(RESET)"
	@if [ ! -f .env ]; then \
		echo "$(AMARILLO)  ⚠ Sin .env — ejecutando make setup...$(RESET)"; \
		$(MAKE) setup; \
	fi
	@$(DOCKER_COMPOSE) up -d
	@echo "$(VERDE)  ✓ App: $(APP_URL)  ·  phpMyAdmin: http://localhost:8081$(RESET)"

down: ## Para y elimina contenedores (conserva volumen de BD)
	@echo "$(ROJO)⛔  Bajando servicios...$(RESET)"
	@$(DOCKER_COMPOSE) down
	@echo "$(VERDE)  ✓ Contenedores detenidos (datos MySQL conservados)$(RESET)"

restart: ## Reinicia los servicios
	@echo "$(CIAN)♻️  Reiniciando...$(RESET)"
	@$(DOCKER_COMPOSE) restart
	@echo "$(VERDE)  ✓ Reinicio completado$(RESET)"

logs: ## Logs del servicio web (follow)
	@$(DOCKER_COMPOSE) logs -f --tail=80 web

ps status: ## Estado de los contenedores
	@echo "$(CIAN)📊  Estado:$(RESET)"
	@$(DOCKER_COMPOSE) ps

rebuild: ## Rebuild sin caché del servicio web + recreate
	@echo "$(MAGENTA)🏗️  build --no-cache web + force-recreate...$(RESET)"
	@$(DOCKER_COMPOSE) build --no-cache web
	@$(DOCKER_COMPOSE) up -d --force-recreate web
	@echo "$(VERDE)  ✓ web recreado$(RESET)"

# ==================== LIMPIEZA ================================================

clean: ## Para contenedores e imágenes locales del compose (mantiene volumen BD)
	@echo "$(ROJO)🧹  Limpieza de contenedores...$(RESET)"
	@$(DOCKER_COMPOSE) down --remove-orphans
	@echo "$(VERDE)  ✓ clean OK (volumen db_data intacto)$(RESET)"

fclean: ## down -v: borra también el volumen MySQL (¡pierdes datos!). Conserva .env
	@echo "$(ROJO)💣  fclean: down -v (borra volumen MySQL)...$(RESET)"
	@$(DOCKER_COMPOSE) down -v --remove-orphans
	@echo "$(AMARILLO)  ℹ .env se conserva. Para borrarlo: make purge-env$(RESET)"
	@echo "$(VERDE)  ✓ fclean OK$(RESET)"

purge-env: ## Elimina el archivo .env
	@echo "$(ROJO)💣  Eliminando .env...$(RESET)"
	@if [ -f .env ]; then rm -f .env; echo "$(VERDE)  ✓ .env eliminado$(RESET)"; \
	else echo "$(AMARILLO)  ⚠ No había .env$(RESET)"; fi

# ==================== INSTALACIÓN =============================================

install: header setup build ## setup + build (sin arrancar)
	@echo "$(VERDE)$(NEGRITA)  ✓ Instalación lista. Siguiente: make up$(RESET)"

all: install up ## setup + build + up (arranque completo)
	@echo "$(VERDE)$(NEGRITA)  ✓ Camagru en marcha → $(APP_URL)$(RESET)"

re: ## fclean + all (BD vacía de nuevo)
	@$(MAKE) fclean
	@$(MAKE) all

# ==================== TESTS ===================================================

test: ## Chequeo rápido Docker + HTTP
	@echo "$(AZUL)🔎  Health check...$(RESET)"
	@docker --version
	@$(DOCKER_COMPOSE) version 2>/dev/null || true
	@$(DOCKER_COMPOSE) ps
	@code=$$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 $(APP_URL)/ 2>/dev/null || echo 000); \
	if [ "$$code" = "200" ] || [ "$$code" = "302" ]; then \
		echo "$(VERDE)  ✓ GET / → HTTP $$code$(RESET)"; \
	else \
		echo "$(AMARILLO)  ⚠ GET / → HTTP $$code (¿make up?)$(RESET)"; \
	fi

test-full: ## Suite automática ./test_auto.sh
	@echo "$(AMARILLO)🧪  ./test_auto.sh$(RESET)"
	@chmod +x ./test_auto.sh 2>/dev/null || true
	@./test_auto.sh

test-eval: ## Guía interactiva de defensa ./test_eval.sh
	@echo "$(AMARILLO)🛡️  ./test_eval.sh$(RESET)"
	@chmod +x ./test_eval.sh 2>/dev/null || true
	@./test_eval.sh

# ==================== UTILIDADES ==============================================

shell: ## Shell bash en el contenedor web
	@$(DOCKER_COMPOSE) exec web bash

db: ## Cliente MySQL en el contenedor db (root; pide password)
	@echo "$(MAGENTA)🗄️  mysql root — password = DB_ROOT_PASS del .env$(RESET)"
	@$(DOCKER_COMPOSE) exec db mysql -u root -p

ext: ## Extensiones PHP: pdo_mysql, gd, imagick, JPEG
	@echo "$(CIAN)🔌  Extensiones en contenedor web:$(RESET)"
	@$(DOCKER_COMPOSE) exec web php -m | grep -iE 'pdo_mysql|^gd$$|imagick' || true
	@$(DOCKER_COMPOSE) exec web php -r '$$i=gd_info(); echo "JPEG Support: ".(!empty($$i["JPEG Support"])?"yes":"no").PHP_EOL;'

permissions: ## Escritura en public/uploads (host + contenedor)
	@echo "$(AMARILLO)📁  public/uploads...$(RESET)"
	@mkdir -p public/uploads/images public/uploads/temp
	@chmod -R 777 public/uploads 2>/dev/null || true
	@$(DOCKER_COMPOSE) exec web bash -c 'mkdir -p /var/www/html/public/uploads/images /var/www/html/public/uploads/temp && chmod -R 777 /var/www/html/public/uploads' 2>/dev/null || true
	@echo "$(VERDE)  ✓ uploads listo$(RESET)"

urls: ## URLs útiles
	@echo "$(VERDE)App:$(RESET)        $(APP_URL)"
	@echo "$(VERDE)phpMyAdmin:$(RESET) http://localhost:8081"
	@echo "$(VERDE)MySQL:$(RESET)      localhost:3306"

open: ## Abre la app en el navegador si es posible
	@xdg-open $(APP_URL) 2>/dev/null || open $(APP_URL) 2>/dev/null || echo "Abre $(APP_URL) manualmente"

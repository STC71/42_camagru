# ============================================================================
#  📸 CAMAGRU - MAKEFILE (125% EDITION) 📸
# ============================================================================
#  Despliegue Contenerizado de Aplicación MVC Personalizada - Escuela 42
# ============================================================================

.PHONY: help build up down restart logs clean fclean install setup test test-full test-eval shell db status all re

# ==================== COLORES ANSI ==========================================
NEGRITA     := \033[1m
RESET       := \033[0m
CIAN        := \033[36m
VERDE       := \033[32m
AMARILLO    := \033[33m
ROJO        := \033[31m
AZUL        := \033[34m
MAGENTA     := \033[35m

# ==================== REGLAS PRINCIPALES ====================================

.DEFAULT_GOAL := help

header:
	@echo "$(CIAN)$(NEGRITA)╔══════════════════════════════════════════════════════════════════════╗$(RESET)"
	@echo "$(CIAN)$(NEGRITA)║ $(VERDE)📸 CAMAGRU DOCKER SYSTEM - Iniciando Proceso de Despliegue MVC     $(CIAN)║$(RESET)"
	@echo "$(CIAN)$(NEGRITA)╚══════════════════════════════════════════════════════════════════════╝$(RESET)"
	@echo ""

help: ## Muestra este menu interactivo
	@echo "$(AZUL)$(NEGRITA)==================================================================================$(RESET)"
	@echo "$(AZUL)$(NEGRITA)                            🛡️  CAMAGRU - AYUDA DE MAKE 🛡️                    $(RESET)"
	@echo "$(AZUL)$(NEGRITA)==================================================================================$(RESET)"
	@echo ""
	@echo "$(VERDE)Comandos Disponibles:$(RESET)"
	@echo ""
	@awk 'BEGIN {FS = ":.*?## "}; /^[a-zA-Z_-]+:.*?## / {printf "%s|%s\n", $$1, $$2}' $(MAKEFILE_LIST) \
		| while IFS='|' read -r cmd desc; do \
			case "$$cmd" in \
				help) emoji="📖 "; color="$(AZUL)";; \
				setup) emoji="🛠️  "; color="$(AMARILLO)";; \
				build) emoji="🏗️  "; color="$(MAGENTA)";; \
				up) emoji="🚀 "; color="$(VERDE)";; \
				down) emoji="⛔ "; color="$(YELLOW)";; \
				restart) emoji="♻️  "; color="$(CIAN)";; \
				logs) emoji="📜 "; color="$(AZUL)";; \
				clean) emoji="🧹 "; color="$(ROJO)";; \
				fclean) emoji="💣 "; color="$(ROJO)";; \
				install) emoji="🧩 "; color="$(AMARILLO)";; \
				test) emoji="🔎 "; color="$(AZUL)";; \
				test-full) emoji="🧪 "; color="$(AMARILLO)";; \
				test-eval) emoji="🛡️  "; color="$(AMARILLO)";; \
				db) emoji="🗄️  "; color="$(MAGENTA)";; \
				shell) emoji="🐚 "; color="$(MAGENTA)";; \
				status) emoji="📊 "; color="$(CIAN)";; \
				all) emoji="⚙️  "; color="$(VERDE)";; \
				re) emoji="🔁 "; color="$(CIAN)";; \
				*) emoji=""; color="$(CIAN)";; \
			esac; \
			printf "  %bmake %-15s%b - %s\n" "$$color$$emoji" "$$cmd" "$(RESET)" "$$desc"; \
		done
	@echo ""

setup: header ## Lanza el Setup Wizard de Variables entorno (.env)
	@echo "$(AMARILLO)🛠️  [*] Iniciando el asistente de configuración del entorno...$(RESET)"
	@./env_setup.sh
	@echo "$(VERDE)  ✓ ¡Entorno (.env) configurado con éxito!$(RESET)"

build: header ## Construye las imagenes Docker necesarias
	@echo "$(MAGENTA)🏗️  [+] Construyendo contenedores de Camagru...$(RESET)"
	docker-compose build
	@echo "$(VERDE)  ✓ ¡Construcción finalizada!$(RESET)"

up: header ## Levanta Camagru en Background
	@echo "$(AMARILLO)🚀  [*] Levantando orquestación de contenedores en background...$(RESET)"
	docker-compose up -d
	@echo "$(VERDE)$(NEGRITA)  ✓ ¡Camagru funcionando perfectamente!$(RESET)"
	@echo "      🌍 App: $(CIAN)http://localhost:8080$(RESET)"
	@echo "      🗄️  DB Admin: $(MAGENTA)http://localhost:8081$(RESET)\n"

down: ## Tumbar los servicios (no rompe Base de datos)
	@echo "$(ROJO)⛔  [-] Deteniendo Camagru y desvinculando red...$(RESET)"
	docker-compose down

restart: down up ## Reiniciar Docker de la aplicacion
	@echo "$(CIAN)♻️  Reiniciando la orquestación de Camagru...$(RESET)"

logs: ## Sigue los Logs en vivo del backend Docker (HTTP)
	@echo "$(AZUL)📜  [*] Siguiendo logs en vivo (Ctrl+C para salir)...$(RESET)"
	docker-compose logs -f

clean: ## Limpieza pura: contenedores huérfanos y caches
	@echo "$(ROJO)🧹  [-] Purgando contenedores y borrando uploads temp...$(RESET)"
	docker-compose down -v
	rm -rf public/uploads/images/*
	@echo "$(VERDE)  ✓ Limpieza de contenedores completada$(RESET)"

fclean: clean ## Limpieza absoluta (Elimina el .env) "Purge mode"
	@echo "$(ROJO)💣  [-] Exterminando configuración local profunda (.env)...$(RESET)"
	@if [ -f .env ]; then \
		rm -f .env; \
		echo "$(VERDE)  ✓ Archivo .env desintegrado$(RESET)"; \
	else \
		echo "$(AMARILLO)  ⚠ Archivo .env no encontrado$(RESET)"; \
	fi
	@echo "$(VERDE)$(NEGRITA)  ✓ ¡Limpieza total absolutizada!$(RESET)"

install: header ## Configura y Buildear absolutamente todo
	@echo "$(AMARILLO)🧩  [*] Iniciando instalación maestra de Camagru...$(RESET)"
	@if [ ! -f .env ]; then \
		echo "$(ROJO)  ⚠ No se encontró archivo .env. Desplegando asistente...$(RESET)"; \
		./env_setup.sh; \
	else \
		echo "$(VERDE)  ✓ Archivo .env existente detectado$(RESET)"; \
	fi
	@echo "$(MAGENTA)[+] Construyendo contenedores...$(RESET)"
	docker-compose build
	@echo "$(VERDE)$(NEGRITA)  ✓ ¡Instalación completa! Ejecuta 'make up' para arrancar$(RESET)\n"

all: install up ## Prepara todo e inicia la aplicación (alias "todo en uno")
	@echo "$(AZUL)⚙️  Ejecutando 'make all' — preparo e inicio completo...$(RESET)"

re: ## Ejecuta `fclean` y luego `all` (limpiar y preparar + arrancar)
	@echo "$(CIAN)🔁  Ejecutando 're' completo: fclean + all...$(RESET)"
	@$(MAKE) fclean
	@$(MAKE) all

# ==================== UTILIDADES BDD (TEST & EVAL) =========================

test: ## Ejecutar la bateria de salud basica Docker
	@echo "$(AZUL)🔎  Lanzando chequeo básico de entorno Docker...$(RESET)"
	@echo "$(VERDE)✓ Verificando Engine...$(RESET) $$(docker --version)"
	@echo "$(VERDE)✓ Verificando Compose...$(RESET) $$(docker-compose --version)"
	@echo "$(CIAN)Estado actual de contenedores:$(RESET)"
	@docker-compose ps

test-full: ## Corre las pruebas unitarias automáticas
	@echo "$(AMARILLO)🧪  Ejecutando batería de pruebas automáticas severas...$(RESET)"
	@./test_auto.sh

test-eval: ## Lanza el Test Virtual Interactico (125% evaluacion guiada)
	@echo "$(AMARILLO)🛡️  Iniciando evaluación interactiva 42 de Camagru...$(RESET)"
	@./test_eval.sh

db: ## Interfaz remota en la consola de la BDD (MySQL root)
	@echo "$(MAGENTA)🗄️  Accediendo a la consola CLI de MySQL...$(RESET)"
	docker-compose exec db mysql -u root -p

shell: ## Lanzar SH proxy shell al servidor Web (Apache)
	@echo "$(MAGENTA)🐚  Infiltrándose en la bash de Apache...$(RESET)"
	docker-compose exec web bash

status: ## Display info de salud de Docker-Camagru
	@echo "$(CIAN)📊  Estado de la flota:$(RESET)"
	@docker-compose ps
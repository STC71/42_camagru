#!/bin/bash

# Camagru Deployment Script
# This script helps deploy and test the Camagru application

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}================================${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Check prerequisites
check_prerequisites() {
    print_header "Checking Prerequisites"
    
    # Check Docker
    if command -v docker &> /dev/null; then
        print_success "Docker is installed"
        docker --version
    else
        print_error "Docker is not installed"
        exit 1
    fi
    
    # Check Docker Compose
    if command -v docker-compose &> /dev/null; then
        print_success "Docker Compose is installed"
        docker-compose --version
    else
        print_error "Docker Compose is not installed"
        exit 1
    fi
    
    echo ""
}

# Setup environment
setup_environment() {
    print_header "Setting Up Environment"
    
    if [ ! -f .env ]; then
        print_warning ".env file not found. Creating from .env.example..."
        cp .env.example .env
        print_success ".env file created"
        print_warning "Please edit .env with your settings before continuing"
        echo ""
        read -p "Press enter when you've configured .env..."
    else
        print_success ".env file already exists"
    fi
    
    echo ""
}

# Check stickers
check_stickers() {
    print_header "Checking Stickers"
    
    sticker_count=$(find public/stickers -name "*.png" 2>/dev/null | wc -l)
    
    if [ "$sticker_count" -eq 0 ]; then
        print_warning "No stickers found in public/stickers/"
        print_warning "Please add PNG stickers with alpha channel"
        echo ""
        echo "You can download free stickers from:"
        echo "  - https://www.flaticon.com/"
        echo "  - https://www.freepik.com/"
        echo ""
        read -p "Press enter when you've added stickers..."
    else
        print_success "Found $sticker_count sticker(s)"
    fi
    
    echo ""
}

# Build containers
build_containers() {
    print_header "Building Docker Containers"
    
    docker-compose build
    
    if [ $? -eq 0 ]; then
        print_success "Containers built successfully"
    else
        print_error "Failed to build containers"
        exit 1
    fi
    
    echo ""
}

# Start application
start_application() {
    print_header "Starting Application"
    
    docker-compose up -d
    
    if [ $? -eq 0 ]; then
        print_success "Application started successfully"
        echo ""
        echo "Access your application at:"
        echo "  • Web App:       http://localhost:8080"
        echo "  • PHPMyAdmin:    http://localhost:8081"
    else
        print_error "Failed to start application"
        exit 1
    fi
    
    echo ""
}

# Wait for services
wait_for_services() {
    print_header "Waiting for Services"
    
    echo "Waiting for database to be ready..."
    sleep 10
    
    # Check if web container is responding
    max_attempts=30
    attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        if curl -s http://localhost:8080 > /dev/null 2>&1; then
            print_success "Web server is responding"
            echo ""
            return 0
        fi
        
        attempt=$((attempt + 1))
        echo -n "."
        sleep 1
    done
    
    print_error "Web server is not responding"
    echo "Check logs with: docker-compose logs"
    return 1
}

# Run tests
run_tests() {
    print_header "Running Basic Tests"
    
    echo "Testing web server..."
    if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200"; then
        print_success "Web server: OK"
    else
        print_warning "Web server: Check response"
    fi
    
    echo "Testing database connection..."
    if docker-compose exec -T db mysql -u root -p$(grep DB_ROOT_PASS .env | cut -d '=' -f2) -e "SELECT 1" > /dev/null 2>&1; then
        print_success "Database: OK"
    else
        print_warning "Database: Check connection"
    fi
    
    echo ""
}

# Show status
show_status() {
    print_header "Container Status"
    docker-compose ps
    echo ""
}

# Main deployment
main() {
    echo -e "${BLUE}"
    echo "╔════════════════════════════════════════╗"
    echo "║     Camagru Deployment Script          ║"
    echo "║     42 School - Web Database           ║"
    echo "╚════════════════════════════════════════╝"
    echo -e "${NC}"
    
    check_prerequisites
    setup_environment
    check_stickers
    build_containers
    start_application
    wait_for_services
    run_tests
    show_status
    
    print_header "Deployment Complete!"
    
    echo "Next steps:"
    echo "  1. Open http://localhost:8080 in your browser"
    echo "  2. Register a new account"
    echo "  3. Check your Mailtrap inbox for verification email"
    echo "  4. Start creating photos!"
    echo ""
    echo "Useful commands:"
    echo "  • View logs:    docker-compose logs -f"
    echo "  • Stop app:     docker-compose down"
    echo "  • Restart:      docker-compose restart"
    echo "  • Clean up:     docker-compose down -v"
    echo ""
    
    print_success "Happy coding! 🚀"
}

# Run main function
main

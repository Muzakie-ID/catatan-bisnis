#!/bin/bash
# docker-deploy.sh - Helper script untuk deploy Docker di VPS

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check requirements
log_info "Checking requirements..."

if ! command -v docker &> /dev/null; then
    log_error "Docker is not installed"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    log_error "Docker Compose is not installed"
    exit 1
fi

log_info "Docker version: $(docker --version)"
log_info "Docker Compose version: $(docker-compose --version)"

# Create network
log_info "Checking mysql_db network..."
if ! docker network ls | grep -q mysql_db; then
    log_warn "Network 'mysql_db' not found. Creating..."
    docker network create mysql_db
    log_info "Network created successfully"
else
    log_info "Network 'mysql_db' already exists"
fi

# Build images
log_info "Building Docker images..."
docker-compose build

# Start containers
log_info "Starting containers..."
docker-compose up -d

# Wait for services
log_info "Waiting for services to start..."
sleep 5

# Check status
log_info "Container status:"
docker-compose ps

# Initialize database
log_info "Initializing database..."

# Check if database exists
DB_CHECK=$(docker exec stok_hp_app mysql -h mysql -u stok_hp -p"${DB_PASS:-password_mysql_anda}" -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='stok_hp'" 2>/dev/null || echo "")

if [ -z "$DB_CHECK" ]; then
    log_warn "Database 'stok_hp' not found. Importing schema..."
    docker exec -i stok_hp_app mysql -h mysql -u stok_hp -p"${DB_PASS:-password_mysql_anda}" < database/database.sql
    log_info "Database schema imported"
else
    log_info "Database already exists"
fi

# Fix permissions
log_info "Setting permissions..."
docker exec stok_hp_app chown -R www-data:www-data /var/www/html
docker exec stok_hp_app chmod -R 755 /var/www/html

# Show access info
echo ""
log_info "=========================================="
log_info "Deployment Complete!"
log_info "=========================================="
echo "Web Access: http://localhost:8092"
echo "Login Page: http://localhost:8092/pages/login.php"
echo ""
echo "Useful commands:"
echo "  View logs:        docker-compose logs -f"
echo "  Stop containers:  docker-compose stop"
echo "  Start containers: docker-compose start"
echo "  Access container: docker exec -it stok_hp_app bash"
echo "  Restart:          docker-compose restart"
echo ""
log_info "=========================================="

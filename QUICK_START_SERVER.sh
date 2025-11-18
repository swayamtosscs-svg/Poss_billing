#!/bin/bash
# Quick start script for production server
# Usage: ./QUICK_START_SERVER.sh

echo "Starting POS Billing System on port 8080..."
php artisan serve --host=0.0.0.0 --port=8080


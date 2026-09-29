# Smart Health Analysis and Advisor

A comprehensive web application for mental health assessment and advisory services at Harare Polytechnic College.

## Features

- **AI-Powered Sentiment Analysis**: Analyzes text responses for emotional content and crisis indicators
- **Risk Assessment**: Multi-factor risk scoring system
- **Video Eye Analysis**: Simulated eye tracking for pupil dilation analysis
- **Personalized Recommendations**: AI-generated action plans based on assessment results
- **Alert System**: Automatic alerts for high-risk students
- **Advisor Dashboard**: Tools for counselors to manage at-risk students
- **Admin Panel**: Complete system management
- **Health Tips**: Curated mental health resources

## Requirements

- PHP 8.1+
- Composer
- SQLite (or MySQL/PostgreSQL)
- Node.js & NPM (for asset compilation)

## Installation

```bash
# Clone the repository
git clone https://github.com/your-repo/smart-health-advisor.git
cd smart-health-advisor

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations and seeders
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Compile assets
npm run build

# Start the development server
php artisan serve
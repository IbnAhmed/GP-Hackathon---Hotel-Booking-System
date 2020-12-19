# GP Hackathon Hotel Booking System

Develop a hotel booking system using REST API. The hotel has 10 rooms, customers need to be registered. Customers can book one room with arrival & checkout time. During booking, the customer can pay the partial or full amount. Later during checkout, will pay any due amounts. Another customer cannot book the same room between the existing arrival & checkout time. There should be a booking list with customer name, booked room number with arrival, checkout date time, and total paid amount.

## Installation

1. Git clone the repo (which you've probably already done).
2. Create a MySQL DB - `CREATE DATABASE gp_hackathon_hbs;`.
3. Copy `.env.example` to `.env` and customize it's database credential (Run it from the project root folder).
5. Install required vendor - `composer install` (Run it from the project root folder). 
5. Migrate all DB table - `php artisan migrate` (Run it from the project root folder).
6. Populate DB with a demo user - `php artisan db:seed`  (Run it from the project root folder).
7. Run the project in local - `php -S localhost:8000 -t public`. Base url will be `http://localhost:8000`.
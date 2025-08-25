<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Order Management System

A simple Order Management System built with Laravel.
This project demonstrates RESTful API development with authentication, order CRUD and status transitions.

# Getting started

## System Requirement

1. PHP 8.2 or above
2. Composer
3. MySQL
4. Apache or Nginx (Web Server)

## Dependencies

- Database must be setup before installing this application

## Installation

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Run migration command

    php artisan migrate

Generate a new application key

    php artisan key:generate

Start the local development server

    php artisan serve

You can now access the website at http:/localhost:8000/


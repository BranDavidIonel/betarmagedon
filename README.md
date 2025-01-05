# Bet Armagedon

## Project Overview

**Bet Armagedon** is a sports betting arbitrage project developed in Laravel 11 and running on PHP 8.2. The project monitors multiple betting websites (e.g., Betano, Superbet, Casa Pariurilor) to detect arbitrage opportunities. Arbitrage betting involves placing bets on all possible outcomes of a sports event in such a way that you make a guaranteed profit, regardless of the final result (win, draw, or lose).

The project scrapes matches from these betting sites and analyzes the odds to identify if there is an opportunity to place bets on each possible outcome, ensuring a profit no matter what the result is.
#### Links from projects:
1 -> / cron get data from sites and stored in DB

2 -> /scraped ( view data from DB and search profit)


## System Requirements

### PHP
- **PHP 8.2** or higher
- Required PHP extensions:
    - `BCMath`
    - `Ctype`
    - `Fileinfo`
    - `JSON`
    - `Mbstring`
    - `OpenSSL`
    - `PDO`
    - `Tokenizer`
    - `XML`

### Composer
Composer is required for managing PHP dependencies.

- To install Composer:
  ```bash
  curl -sS https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/local/bin/composer
### Selenium
Selenium is required for web scraping functionality in the project.
### MySQL 8.0
MySQL 8.0 or higher is required for managing the database.

## Setup and Installation
  ```bash
  
composer install  
#Create an alias for Sail (this is optional, but simplifies using Sail commands)'
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
sail up -d
sail artisan migrate
sail artisan db:seed

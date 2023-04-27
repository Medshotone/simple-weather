# About Project

Project created to allow clients to self register accounts to gain login access on xyz applications.

The main features are:
- Creating an account through a form or via Google SSO.
- Creating a Bearer token API to access the site data via the API.
- Receiving data from xyz applications (Open Weather) through a website page or using the API.

# Building

Current project uses [Laravel Sail](https://laravel.com/docs/8.x/sail#main-content).
Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker development environment.

This is list with steps what need to do for build project with docker + sail.
- $cat autoload.sh | bash.
- Set up in .env google SSO keys: GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET
- Set up in .env open weather api key [account on openweathermap API](https://openweathermap.org/price): OPENWEATHERMAP_API_KEY
- ./vendor/bin/sail php artisan test

If you wanted to use sail instead ./vendor/bin/sail you need to create [Laravel Sail Aliase](https://laravel.com/docs/8.x/sail#configuring-a-bash-alias)

# Routes

## Routes: Public

### Web
- GET /login - Simple login page for users.
- GET /register - Simple register page for users.
- GET /login/google -  Uses for login with Google SSO. Link on this route located on page /login in login form.
- GET /home - Main page for authenticated users.

### API
- POST /api/register - register with name + email and generate API Bearer token
- POST /api/login - login with name + email for generate API Bearer token
- POST /login/google - (login or register) with Google access_token for generate API Bearer token 

## Routes: API with accesses by Bearer token

- GET /api/home - API home page for get weather data as json

# License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

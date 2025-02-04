# **Appointment Manager** 🗓️

A Laravel + Vue.js application to manage users and their appointments with a dashboard overview.

## **Features** ✨

✅ User creation and management  
✅ Appointment scheduling  
✅ Dashboard with appointment and user statistics  
✅ RESTful API with authentication

## Screenshots 📸

Users view:  
![Users Screenshot](screenshots/users.png)

Appointments view:  
![Appointments Screenshot](screenshots/appointments.png)

## **Tech Stack** 🛠️

-   **Backend:** Laravel
-   **Frontend:** Vue.js
-   **Database:** MySQL

## **Installation** ⚙️

1. Clone the repository:
    ```sh
    git clone https://github.com/ilxBoschetto/laravel-vue-project.git
    cd appointment-manager
    ```
2. Install dependencies:
    ```sh
    composer install
    npm install
    ```
3. Configure the environment file:
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```
4. Set up the database and run migrations:
    ```sh
    php artisan migrate
    ```
5. Start the development server:
    ```sh
    php artisan serve
    npm run dev
    ```

## **Usage** 🚀

-   Register/Login
-   Create users and schedule appointments
-   View dashboard statistics

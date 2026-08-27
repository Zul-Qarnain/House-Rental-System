<?php
// Single Entry Point / Front Controller for InfinityFree htdocs / Local Web Root
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/../core/' . $class . '.php',
        __DIR__ . '/../models/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

Auth::start();

$router = new Router();

// Public Routes
$router->add('GET', '/', 'PropertyController@index');
$router->add('GET', '/property/:id', 'PropertyController@detail');

// Auth Routes
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@processLogin');
$router->add('GET', '/register', 'AuthController@showRegister');
$router->add('POST', '/register', 'AuthController@processRegister');
$router->add('POST', '/logout', 'AuthController@logout');
$router->add('GET', '/forgot-password', 'AuthController@showForgotPassword');
$router->add('POST', '/forgot-password', 'AuthController@processForgotPassword');
$router->add('GET', '/reset-password', 'AuthController@showResetPassword');
$router->add('POST', '/reset-password', 'AuthController@processResetPassword');

// Tenant Routes
$router->add('GET', '/tenant/dashboard', 'RentalController@tenantDashboard');
$router->add('GET', '/rentals/apply/:id', 'RentalController@showApplicationForm');
$router->add('POST', '/rentals/apply', 'RentalController@processRequest');
$router->add('POST', '/reviews/submit', 'ReviewController@processSubmit');

// Owner Routes
$router->add('GET', '/owner/dashboard', 'RentalController@ownerDashboard');
$router->add('GET', '/properties/create', 'PropertyController@showCreateForm');
$router->add('POST', '/properties/create', 'PropertyController@processCreate');
$router->add('POST', '/properties/toggle-status', 'PropertyController@toggleAvailability');
$router->add('POST', '/rentals/decision', 'RentalController@processDecision');
$router->add('POST', '/reviews/reply', 'ReviewController@processReply');

// Broker Routes
$router->add('GET', '/broker/dashboard', 'BrokerController@dashboard');
$router->add('POST', '/broker/visits/schedule', 'BrokerController@scheduleVisit');
$router->add('POST', '/broker/visits/status', 'BrokerController@updateVisitStatus');

// Admin Routes
$router->add('GET', '/admin/users', 'AdminController@users');
$router->add('POST', '/admin/users/status', 'AdminController@toggleUserStatus');
$router->add('POST', '/admin/properties/approve', 'AdminController@approveProperty');
$router->add('POST', '/admin/properties/assign-broker', 'AdminController@assignBroker');
$router->add('POST', '/admin/complaints/resolve', 'AdminController@resolveComplaint');

// Shared Messaging & Notification Routes
$router->add('GET', '/messages', 'MessageController@index');
$router->add('POST', '/messages/send', 'MessageController@send');
$router->add('POST', '/notifications/read', 'NotificationController@markAsRead');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

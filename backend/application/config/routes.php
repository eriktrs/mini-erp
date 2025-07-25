<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

/*
|--------------------------------------------------------------------------
| Default Controller
|--------------------------------------------------------------------------
*/
$route['default_controller'] = 'welcome';

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/
$route['products'] = 'ProductController/index';                     // List all products
$route['products/create'] = 'ProductController/create';             // Show product creation form
$route['products/store'] = 'ProductController/store';               // Handle new product creation
$route['products/edit/(:num)'] = 'ProductController/edit/$1';       // Show edit form
$route['products/update/(:num)'] = 'ProductController/update/$1';   // Handle product update
$route['products/delete/(:num)'] = 'ProductController/delete/$1';   // Delete a product

/*
|--------------------------------------------------------------------------
| Coupon Routes
|--------------------------------------------------------------------------
*/
$route['coupons'] = 'CouponController/index';                       // List coupons
$route['coupons/create'] = 'CouponController/create';               // Show coupon creation form
$route['coupons/store'] = 'CouponController/store';                 // Handle coupon creation
$route['coupons/edit/(:num)'] = 'CouponController/edit/$1';         // Show edit coupon form
$route['coupons/update/(:num)'] = 'CouponController/update/$1';     // Update coupon
$route['coupons/delete/(:num)'] = 'CouponController/delete/$1';     // Delete coupon

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
*/
$route['order/cart'] = 'OrderController/cart';                      // View cart
$route['order/add'] = 'OrderController/add_to_cart';                // Add product to cart
$route['order/checkout'] = 'OrderController/checkout';              // Checkout page
$route['order/place'] = 'OrderController/place_order';              // Place order

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
*/
$route['webhook/order-status'] = 'WebhookController/update_order_status'; // Webhook for updating order status

/*
|--------------------------------------------------------------------------
| 404 and URI Config
|--------------------------------------------------------------------------
*/
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

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
| API Routes - Products
|--------------------------------------------------------------------------
*/
$route['products']['GET'] = 'ProductController/index';                  // List all products
$route['products']['POST'] = 'ProductController/store';                 // Create product
$route['products/(:num)']['GET'] = 'ProductController/show/$1';         // Get product by ID
$route['products/(:num)']['PUT'] = 'ProductController/update/$1';       // Update product
$route['products/(:num)']['DELETE'] = 'ProductController/delete/$1';    // Delete product

/*
|--------------------------------------------------------------------------
| API Routes - Coupons
|--------------------------------------------------------------------------
*/
$route['coupons']['GET'] = 'CouponController/index';                    // List coupons
$route['coupons']['POST'] = 'CouponController/store';                   // Create coupon
$route['coupons/(:num)']['GET'] = 'CouponController/show/$1';           // Show coupon
$route['coupons/(:num)']['PUT'] = 'CouponController/update/$1';         // Update coupon
$route['coupons/(:num)']['DELETE'] = 'CouponController/delete/$1';      // Delete coupon

/*
|--------------------------------------------------------------------------
| API Routes - Orders
|--------------------------------------------------------------------------
*/
$route['orders/cart']['GET'] = 'OrderController/cart';                            // List cart items
$route['orders/cart']['POST'] = 'OrderController/addToCart';                      // Add item to cart
$route['orders/cart/(:num)']['DELETE'] = 'OrderController/removeFromCart/$1';     // Remove item from cart
$route['orders/cart/(:num)']['PUT'] = 'OrderController/updateQuantity/$1';        // Update cart item quantity
$route['orders/coupon']['POST'] = 'OrderController/applyCoupon';                  // Apply coupon to cart
$route['orders/checkout']['POST'] = 'OrderController/checkout';                   // Checkout and create order
$route['orders/(:num)']['GET'] = 'OrderController/details/$1';                    // Get order details by ID
$route['orders']['GET'] = 'OrderController/index';                                 // List all orders

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
*/
$route['webhook/order-status']['POST'] = 'WebhookController/update_order_status';

/*
|--------------------------------------------------------------------------
| 404 and URI Config
|--------------------------------------------------------------------------
*/
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

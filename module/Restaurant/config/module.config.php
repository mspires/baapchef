<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Restaurant\Controller\Restaurant' => 'Restaurant\Controller\RestaurantController',
            'Restaurant\Controller\Auth' => 'Restaurant\Controller\AuthController',
            'Restaurant\Controller\Home' => 'Restaurant\Controller\HomeController',
            'Restaurant\Controller\Profile' => 'Restaurant\Controller\ProfileController',
            'Restaurant\Controller\Address' => 'Restaurant\Controller\AddressController',
            'Restaurant\Controller\User' => 'Restaurant\Controller\UserController',
            'Restaurant\Controller\Order' => 'Restaurant\Controller\OrderController',
            'Restaurant\Controller\OrderItem' => 'Restaurant\Controller\OrderitemController',
            'Restaurant\Controller\Dish' => 'Restaurant\Controller\DishController',
            'Restaurant\Controller\Dishgroup' => 'Restaurant\Controller\DishgroupController',
            'Restaurant\Controller\TakeoutDish' => 'Restaurant\Controller\TakeoutDishController',
            'Restaurant\Controller\Customer' => 'Restaurant\Controller\CustomerController',
            'Restaurant\Controller\Membership' => 'Restaurant\Controller\MembershipController',
            'Restaurant\Controller\Payment' => 'Restaurant\Controller\PaymentController',
            'Restaurant\Controller\Creditcard' => 'Restaurant\Controller\CreditcardController',
            'Restaurant\Controller\Table' => 'Restaurant\Controller\TableController',
            'Restaurant\Controller\Reservation' => 'Restaurant\Controller\ReservationController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'restaurant' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Restaurant',
                        'action'     => 'index',
                    ),
                ),
            ),
            'address' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/address[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Address',
                        'action'     => 'index',
                    ),
                ),
            ),

            'restaurantauth' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/auth[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'restauranthome' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/home[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'restprofile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/profile[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Profile',
                        'action'     => 'index',
                    ),
                ),
            ),

            'restuser' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'restdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/dish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Dish',
                        'action'     => 'index',
                    ),
                ),
            ),
            'resttakeoutdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/takeoutdish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\TakeoutDish',
                        'action'     => 'index',
                    ),
                ),
            ),            
            
            'restdishgroup' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/dishgroup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Dishgroup',
                        'action'     => 'index',
                    ),
                ),
            ),
            'restorder' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/order[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ),            
            'restorderitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/orderitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\OrderItem',
                        'action'     => 'index',
                    ),
                ),
            ),
            'restcustomer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/customer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Customer',
                        'action'     => 'index',
                    ),
                ),
            ),
            'restmembership' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/membership[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Membership',
                        'action'     => 'index',
                    ),
                ),
            ),
            'restpayment' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/payment[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Payment',
                        'action'     => 'index',
                    ),
                ),
            ),
            'restcredit' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/creditcard[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Creditcard',
                        'action'     => 'index',
                    ),
                ),
            ),
            'resttable' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/table[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Table',
                        'action'     => 'index',
                    ),
                ),
            ),            
            'restreservation' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/restaurant/reservation[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Restaurant\Controller\Reservation',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Restaurant' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'restaurant/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'restpinin' => __DIR__ . '/../view/restaurant/home/pinin.phtml',
            'resthomeme' => __DIR__ . '/../view/restaurant/home/me.phtml',
            'resthomeme2' => __DIR__ . '/../view/restaurant/home/me2.phtml',
        ),
    ),
);

<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Customer\Controller\Customer' => 'Customer\Controller\CustomerController',
            'Customer\Controller\Auth' => 'Customer\Controller\AuthController',
            'Customer\Controller\Home' => 'Customer\Controller\HomeController',
            'Customer\Controller\Profile' => 'Customer\Controller\ProfileController',
            'Customer\Controller\Restaurant' => 'Customer\Controller\RestaurantController',
            'Customer\Controller\Cart' => 'Customer\Controller\CartController',
            'Customer\Controller\CartItem' => 'Customer\Controller\CartItemController',
            'Customer\Controller\Order' => 'Customer\Controller\OrderController',
            'Customer\Controller\OrderItem' => 'Customer\Controller\OrderItemController',
            'Customer\Controller\TakeoutDish' => 'Customer\Controller\TakeoutDishController',
            'Customer\Controller\TakeoutCart' => 'Customer\Controller\TakeoutCartController',
            'Customer\Controller\TakeoutCartItem' => 'Customer\Controller\TakeoutCartItemController',
            'Customer\Controller\Dish' => 'Customer\Controller\DishController',
            'Customer\Controller\Membership' => 'Customer\Controller\MembershipController',
            'Customer\Controller\Reward' => 'Customer\Controller\RewardController',
            'Customer\Controller\Message' => 'Customer\Controller\MessageController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'customer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Customer',
                        'action'     => 'index',
                    ),
                ),
            ),

            'customerauth' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/auth[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'customerhome' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/home[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),

            'myprofile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/profile[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Profile',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'myrestaurant' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/restaurant[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Restaurant',
                        'action'     => 'index',
                    ),
                ),
            ),    
            'mycart' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/cart[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Cart',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mycartitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/cartitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\CartItem',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mytakeoutcart' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/takeoutcart[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\TakeoutCart',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mytakeoutcartitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/takeoutcartitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\TakeoutCartItem',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mytakeoutdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/takeoutdish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\TakeoutDish',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mydish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/dish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Dish',
                        'action'     => 'index',
                    ),
                ),
            ),
            'myorder' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/order[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ),
            'myorderitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/orderitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\OrderItem',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mytakeoutorder' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/order[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mytakeoutorderitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/orderitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\OrderItem',
                        'action'     => 'index',
                    ),
                ),
            ),
            'mymembership' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/membership[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Membership',
                        'action'     => 'index',
                    ),
                ),
            ),  
            'myreward' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/reward[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Reward',
                        'action'     => 'index',
                    ),
                ),
            ),    
            'mymessage' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/customer/message[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Customer\Controller\Message',
                        'action'     => 'index',
                    ),
                ),
            ),                              
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Customer' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'customer/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'passwd' => __DIR__ . '/../view/customer/home/passwd.phtml',
            'email/tpl/layout'  => __DIR__ . '/../view/email/tpl/layout.phtml',
            'email/tpl/contact' => __DIR__ . '/../view/email/tpl/contact.phtml',
            'email/tpl/signup' => __DIR__ . '/../view/email/tpl/signup.phtml',
            'email/tpl/forgetpwd' => __DIR__ . '/../view/email/tpl/forgetpwd.phtml',
            'email/tpl/notice' => __DIR__ . '/../view/email/tpl/notice.phtml',
            'email/tpl/alert' => __DIR__ . '/../view/email/tpl/alert.phtml',
            'email/tpl/message' => __DIR__ . '/../view/email/tpl/message.phtml',
            'email/tpl/receipt' => __DIR__ . '/../view/email/tpl/receipt.phtml',
        ),
    ),
);

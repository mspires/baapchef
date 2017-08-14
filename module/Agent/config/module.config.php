<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Agent\Controller\Agent' => 'Agent\Controller\AgentController',
            'Agent\Controller\Auth' => 'Agent\Controller\AuthController',
            'Agent\Controller\Home' => 'Agent\Controller\HomeController',
            'Agent\Controller\Profile' => 'Agent\Controller\ProfileController',
            'Agent\Controller\User' => 'Agent\Controller\UserController',
            'Agent\Controller\Restaurant' => 'Agent\Controller\RestaurantController',
            'Agent\Controller\Address' => 'Agent\Controller\AddressController',
            'Agent\Controller\Customer' => 'Agent\Controller\CustomerController',
            'Agent\Controller\Order' => 'Agent\Controller\OrderController',
            'Agent\Controller\OrderItem' => 'Agent\Controller\OrderItemController',
            'Agent\Controller\Dish' => 'Agent\Controller\DishController',
            'Agent\Controller\Dishgroup' => 'Agent\Controller\DishgroupController',
            'Agent\Controller\TakeoutDish' => 'Agent\Controller\TakeoutDishController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'agent' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Agent',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'agentauth' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/auth[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'agenthome' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/home[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),

            'agprofile' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/profile[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Profile',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'aguser' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'agrestaurant' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/restaurant[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Restaurant',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'agrestaddress' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/address[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Address',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'agcustomer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/customer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Customer',
                        'action'     => 'index',
                    ),
                ),
            ),

            'agdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/dish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Dish',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'agdishgroup' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/dishgroup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Dishgroup',
                        'action'     => 'index',
                    ),
                ),
            ), 
              
            'agtakeoutdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/takeoutdish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\TakeoutDish',
                        'action'     => 'index',
                    ),
                ),
            ),
            'agorder' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/order[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ), 
            'agorderitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/agent/orderitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Agent\Controller\OrderItem',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Agent' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'agent/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'agpinin' => __DIR__ . '/../view/agent/home/pinin.phtml',
            'aghomeme' => __DIR__ . '/../view/agent/home/me.phtml',
            'agent/email/tpl/layout'  => __DIR__ . '/../view/email/tpl/layout.phtml',
            'agent/email/tpl/contact' => __DIR__ . '/../view/email/tpl/contact.phtml',
            'agent/email/tpl/signup' => __DIR__ . '/../view/email/tpl/signup.phtml',
            'agent/email/tpl/forgetpwd' => __DIR__ . '/../view/email/tpl/forgetpwd.phtml',
            'agent/email/tpl/event' => __DIR__ . '/../view/email/tpl/event.phtml',
            'agent/email/tpl/notice' => __DIR__ . '/../view/email/tpl/notice.phtml',
            'agent/email/tpl/alert' => __DIR__ . '/../view/email/tpl/alert.phtml',
            'agent/email/tpl/message' => __DIR__ . '/../view/email/tpl/message.phtml',
            'agent/email/tpl/request' => __DIR__ . '/../view/email/tpl/request.phtml',
            'agent/email/tpl/response' => __DIR__ . '/../view/email/tpl/response.phtml',
        ),
    ),
);

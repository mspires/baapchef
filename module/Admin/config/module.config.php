<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
            'Admin\Controller\Auth' => 'Admin\Controller\AuthController',
            'Admin\Controller\Home' => 'Admin\Controller\HomeController',
            'Admin\Controller\Profile' => 'Admin\Controller\ProfileController',
            'Admin\Controller\User' => 'Admin\Controller\UserController',
            'Admin\Controller\Restaurant' => 'Admin\Controller\RestaurantController',
            'Admin\Controller\Address' => 'Admin\Controller\AddressController',
            'Admin\Controller\Agent' => 'Admin\Controller\AgentController',
            'Admin\Controller\Customer' => 'Admin\Controller\CustomerController',
            'Admin\Controller\Dish' => 'Admin\Controller\DishController',
            'Admin\Controller\Dishgroup' => 'Admin\Controller\DishgroupController',
            'Admin\Controller\TakeoutDish' => 'Admin\Controller\TakeoutDishController',
            'Admin\Controller\Order' => 'Admin\Controller\OrderController',
            'Admin\Controller\OrderItem' => 'Admin\Controller\OrderItemController',
            'Admin\Controller\Event' => 'Admin\Controller\EventController',
            'Admin\Controller\Alert' => 'Admin\Controller\AlertController',
            'Admin\Controller\Message' => 'Admin\Controller\MessageController',
            'Admin\Controller\Reward' => 'Admin\Controller\RewardController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/admin',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Admin',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),

            'adminauth' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/auth[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Auth',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'adminhome' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/home[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),

            'adminuser' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/user[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
                        
            'adminrestaurant' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/restaurant[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Restaurant',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'adminrestaddress' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/address[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Address',
                        'action'     => 'index',
                    ),
                ),
            ),
                        
            'adminagent' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/agent[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Agent',
                        'action'     => 'index',
                    ),
                ),
            ),
                        
            'admincustomer' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/customer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Customer',
                        'action'     => 'index',
                    ),
                ),
            ),

            'admindish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/dish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Dish',
                        'action'     => 'index',
                    ),
                ),
            ),
                        
            'admindishgroup' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/dishgroup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\DishGroup',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'admintakeoutdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/takeoutdish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\TakeoutDish',
                        'action'     => 'index',
                    ),
                ),
            ),
                        
            'adminorder' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/order[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'adminorderitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/orderitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\OrderItem',
                        'action'     => 'index',
                    ),
                ),
            ),

            'adminevent' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/event[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Event',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'adminalert' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/alert[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Alert',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'adminmessage' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/message[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Message',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'adminreward' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/admin/reward[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Reward',
                        'action'     => 'index',
                    ),
                ),
            ),            
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Admin' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'admin/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'pinin' => __DIR__ . '/../view/admin/home/pinin.phtml',
            'homeme' => __DIR__ . '/../view/admin/home/me.phtml',
            'admin/email/tpl/layout'  => __DIR__ . '/../view/email/tpl/layout.phtml',
            'admin/email/tpl/contact' => __DIR__ . '/../view/email/tpl/contact.phtml',
            'admin/email/tpl/signup' => __DIR__ . '/../view/email/tpl/signup.phtml',
            'admin/email/tpl/resetpwd' => __DIR__ . '/../view/email/tpl/resetpwd.phtml',
            'admin/email/tpl/forgetpwd' => __DIR__ . '/../view/email/tpl/forgetpwd.phtml',
            'admin/email/tpl/event' => __DIR__ . '/../view/email/tpl/event.phtml',
            'admin/email/tpl/notice' => __DIR__ . '/../view/email/tpl/notice.phtml',
            'admin/email/tpl/alert' => __DIR__ . '/../view/email/tpl/alert.phtml',
            'admin/email/tpl/message' => __DIR__ . '/../view/email/tpl/message.phtml',
            'admin/email/tpl/request' => __DIR__ . '/../view/email/tpl/request.phtml',
            'admin/email/tpl/response' => __DIR__ . '/../view/email/tpl/response.phtml',
        ),
    ),
);

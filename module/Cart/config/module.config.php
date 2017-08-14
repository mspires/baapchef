<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Cart\Controller\Cart' => 'Cart\Controller\CartController',
            'Cart\Controller\Cartitem' => 'Cart\Controller\CartitemController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'cart' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cart[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Cart\Controller\Cart',
                        'action'     => 'index',
                    ),
                ),
            ),
            'cartitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/cartitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Cart\Controller\CartItem',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Cart' => __DIR__ . '/../view',
        ),
    ),
);

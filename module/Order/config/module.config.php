<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Order\Controller\Order' => 'Order\Controller\OrderController',
            'Order\Controller\OrderItem' => 'Order\Controller\OrderItemController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'order' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/order[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Order\Controller\Order',
                        'action'     => 'index',
                    ),
                ),
            ),
            'orderitem' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/orderitem[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Order\Controller\OrderItem',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Order' => __DIR__ . '/../view',
        ),
    ),
);

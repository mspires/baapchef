<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Waiting\Controller\Waiting' => 'Waiting\Controller\WaitingController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'waiting' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/waiting[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Waiting\Controller\Waiting',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Waiting' => __DIR__ . '/../view',
        ),
    ),
);

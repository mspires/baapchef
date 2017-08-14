<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Alert\Controller\Alert' => 'Alert\Controller\AlertController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'alert' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/alert[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Alert\Controller\Alert',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Alert' => __DIR__ . '/../view',
        ),
    ),
);

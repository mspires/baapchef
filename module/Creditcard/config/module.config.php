<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Creditcard\Controller\Creditcard' => 'Creditcard\Controller\CreditcardController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'creditcard' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/creditcard[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Creditcard\Controller\Creditcard',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Creditcard' => __DIR__ . '/../view',
        ),
    ),
);

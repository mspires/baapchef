<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Membership\Controller\Membership' => 'Membership\Controller\MembershipController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'membership' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/membership[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Membership\Controller\Membership',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Membership' => __DIR__ . '/../view',
        ),
    ),
);

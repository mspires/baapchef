<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Reward\Controller\Reward' => 'Reward\Controller\RewardController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'reward' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/reward[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Reward\Controller\Reward',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Reward' => __DIR__ . '/../view',
        ),
    ),
);

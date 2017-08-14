<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Event\Controller\Event' => 'Event\Controller\EventController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'event' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/event[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Event\Controller\Event',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Event' => __DIR__ . '/../view',
        ),
    ),
);

<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Table\Controller\Table' => 'Table\Controller\TableController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'table' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/table[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Table\Controller\Table',
                        'action'     => 'index',
                    ),
                ),
            ),
       ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Table' => __DIR__ . '/../view',
        ),
    ),
);

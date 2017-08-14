<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Dish\Controller\Dish' => 'Dish\Controller\DishController',
            'Dish\Controller\Dishgroup' => 'Dish\Controller\DishgroupController',
            'Dish\Controller\TakeoutDish' => 'Dish\Controller\TakeoutDishController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'dish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/dish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Dish\Controller\Dish',
                        'action'     => 'index',
                    ),
                ),
            ),
            'dishgroup' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/dishgroup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Dish\Controller\Dishgroup',
                        'action'     => 'index',
                    ),
                ),
            ),
            'takeoutdish' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/takeoutdish[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Dish\Controller\TakeoutDish',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Dish' => __DIR__ . '/../view',
        ),
    ),
);

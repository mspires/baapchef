<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'System\Controller\System' => 'System\Controller\SystemController',
            'System\Controller\Role' => 'System\Controller\RoleController',
            'System\Controller\Resource' => 'System\Controller\ResourceController',
            'System\Controller\Permission' => 'System\Controller\PermissionController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'system' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/system',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'System\Controller',
                        'controller'    => 'System',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'System' => __DIR__ . '/../view',
        ),
    ),
);

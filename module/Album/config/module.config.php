<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'album' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/album[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Album\Controller\Album',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
   
    
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
        
//     'view_manager' => array(
//         'strategies' => array(
//             'ViewJsonStrategy',
//         ),
//     )
//     ,
//     'doctrine' => array(
//         'driver' => array(
//             'album_entities' => array(
//                 'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
//                 'cache' => 'array',
//                 'paths' => array(__DIR__ . '/../src/Album/Model')
//             ),
//             'orm_default' => array(
//                 'drivers' => array(
//                     'Album\Model' => 'album_entities'
//                 )
//             )
//         )
//     )
);

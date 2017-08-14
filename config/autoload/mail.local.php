<?php

return array(
    'mail' => array(
        'transport' => array(
            'options' => array(
                'host'              => 'smtp.gmail.com',
                'port'              => 587,
    		'connection_class'  => 'login',
		'connection_config' => array(
                    'username' => 'eaglemay@gmail.com',
                    'password' => 'andrew0414!',
                    //'port' => '587',
                    'ssl' => 'tls',
                    //'auth' => 'login',
                ),
            ),
        ),
    ),
);

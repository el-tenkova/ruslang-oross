<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Navigation\Service\NavigationAbstractServiceFactory;
//use stdClass;

return [
	'navigation' => [
		'admin' => [
			[
				'label' => 'Статьи',
				'route' => 'dict',
			],
			[
				'label' => 'Правки в словаре/Индексация',
				'route' => 'edit',
			],
			[
				'label' => 'Добавить пользователя',
				'route' => 'user',
			],
			[
				'label' => 'Выгрузить словари',
				'route' => 'getdics',
			],
			[
				'label' => 'Выйти',
				'route' => 'logout',
			],
		],
    ],

    'router' => [
        'routes' => [
            'adminos' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/adminos[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Controller\AdminRosanController',
                        'action'     => 'index',
                    ],
                ], 
            ],
            'dict' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/dict[/:action][/:id][page/:page]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ],
                    'defaults' => [
                        'controller' => 'Controller\AdminRosanController',
                        'action'     => 'dict',
                        'id' => '0',
                        'page' => 1,
                    ],
                ], 
            ],
            'edit' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/edit[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Controller\AdminRosanController',
                        'action'     => 'edit',
                    ],
                ], 
            ],
            'user' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/user[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Controller\AdminRosanController',
                        'action'     => 'user',
                    ],
                ], 
            ],
            'getdics' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/getdics[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Controller\AdminRosanController',
                        'action'     => 'getdics',
                    ],
                ], 
            ],
            'logout' => [
                'type'    => 'segment',
                'options' => [
                    'route'       => '/logout[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => 'Controller\AdminRosanController',
                        'action'     => 'logout',
                    ],
                ], 
            ],            
        ],
    ],

    'service_manager' => [
        'abstract_factories' => [
            'Zend\Navigation\Service\NavigationAbstractServiceFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'Controller\AdminRosanController' => 'Admin\Factory\AdminControllerFactory',
        ],
   //     'invokables' => [
    //        'Admin\Controller\AdminRosanController' => 'Admin\Controller\AdminRosanController',
    //    ],
    ],
    
    'view_manager' => [
        'template_path_stack' => [
            'admin-rosan' => __DIR__ . '/../view',
        ],
        'template_map' => array(
            'layout/admin' => __DIR__ . '/../view/layout/admin.phtml',
        ),
    ],
];
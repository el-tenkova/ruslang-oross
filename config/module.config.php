<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
	'navigation' => array(
		'default' => array(
			array(
				'label' => 'Главная',
				'route' => 'home',
			),
			array(
				'label' => 'Поиск',
				'route' => 'search',
        	    'action' => 'index',
			),
	        array(
    	        'label' => 'Орфограммы и формулы',
        	    'route' => 'orthogr',
        	    'action' => 'index',
			),
	        array(
    	        'label' => 'Справочник',
        	    'route' => 'paragraph',
        	    'action' => 'view',
			),
	        array(
    	        'label' => 'Литература',
        	    'route' => 'home',
        	    'action' => 'literature',
			),
/*	        array(
    	        'label' => 'Научная статья по проекту',
                'uri'   => '/doc/ot_orph_slovarya_k_resursu.pdf'
			), */
		),
	),
    'router' => array(
        'routes' => array(
            'home' => array(
				'type' => 'segment', //'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'       => '/[:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
//                        'controller' => 'Contents\Controller\Contents',
                        'action'     => 'index',
                    ),
                ),
            ),
            'paragraph' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/paragraph[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Paragraph',
                        'action'     => 'view',
                        'id' => '1',
                    ),
                ), 
            ),
            'article' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/article[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Articles',
                        'action'     => 'view',
                        'id' => '1',
                    ),
                ), 
            ),
            //////////////
            'orthogr' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/orthogr[/:action][id/:id][page/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Orthogr',
                        'action'     => 'index',
                        'page'		 => 1,
                    ),
                ), 
            ),
            'search' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/search[/:action][page/:page]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Search',
                        'action'     => 'index',
                        'page'		 => 1,
                    ),
                ), 
            ),
            'download' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'       => '/download[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Download',
                        'action'     => 'do',
                    ),
                ), 
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            'ContentsMenu' => 'Contents\ContentsMenuFactory',
            'SiteState' => 'Contents\StateFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),

    'ContentsMenu' => array('1' => '1'),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Search' => 'Application\Controller\SearchController',
            'Application\Controller\Orthogr' => 'Application\Controller\OrthogrController',
            'Application\Controller\Paragraph' => 'Application\Controller\ParagraphController',
            'Application\Controller\Download' => 'Application\Controller\DownloadController',
            'Application\Controller\Articles' => 'Application\Controller\ArticlesController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),	        
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);

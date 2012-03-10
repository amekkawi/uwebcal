<?php

/**
 * Base configuration for WebApplication.
 * 
 * DO NOT EDIT THIS FILE. It will be overwritten when you update UWebCal.
 * Instead, include your custom configuration in web-local.php in this directory.
 * 
 * @author André Mekkawi <uwebcal@andremekkawi.com>
 * @link http://www.uwebcal.com/
 * @copyright Copyright &copy; André Mekkawi
 * @license http://www.uwebcal.com/license/
 */

return array(
	'name'=>'My Calendar',
	
	// components to preload
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.auth.*',
		'application.components.auth.rbac.*',
		'application.components.fields.*',
		'application.components.db.*',
		'application.components.db.mssql.*',
		'application.components.db.mysql.*',
		'application.components.db.oci.*',
		'application.components.db.pgsql.*',
		'application.components.db.sqlite.*',
),

	// application components
	'components'=>array(
		'db'=>array(
			'class' => 'DbConnection',
			'enableProfiling' => defined('YII_DEBUG') && YII_DEBUG,
			'charset' => 'utf8',
			'tablePrefix'=>'',
			'schemaCachingDuration'=>300,
		),
		'clientScript'=>array(
			'packages'=>array(
				'app'=>array(
					'baseUrl'=>'',
					'depends'=>array('app.css','app.js')
				),
				'app.css'=>array(
					'baseUrl'=>'css',
					'css'=>array(
						'base.css'
					),
					'depends'=>array('yui3.css')
				),
				'app.js'=>array(
					'baseUrl'=>'js',
					'js'=>array(
						'extensions.js',
						'jquery.extensions.js'
					),
					'depends'=>array('jquery')
				),
				'jquery'=>array(
					'baseUrl'=>'http://ajax.googleapis.com/ajax/libs/jquery/',
					'js'=>array(
						'1.7.1/jquery.min.js'
					)
				),
				'yui3'=>array(
					'baseUrl'=>'',
					'depends'=>array('yui3.css')
				),
				'yui3.css'=>array(
					'baseUrl'=>'http://yui.yahooapis.com/3.4.1/build/',
					'css'=>array(
						'cssreset-context/cssreset-context-min.css',
						'cssfonts-context/cssfonts-context-min.css',
						'cssbase-context/cssbase-context-min.css',
						'cssgrids/grids-min.css'
					)
				)
			)
		),
		'user'=>array(
			'class'=>'WebUser',
			'allowAutoLogin'=>false,
			'loginUrl'=>array('auth/login'),
			'authTimeout'=>1800,
			'loginRequiredAjaxResponse'=>json_encode(array(
				'result'=>false,
				'reason'=>'loginrequired'
			)),
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>defined('YII_DEBUG') && YII_DEBUG,
			'useStrictParsing'=>true,
			'appendParams'=>false,
			'rules'=>array(
		
				'' => array('events', 'parsingOnly'=>true),
				
				// Export Routes
				//'export/rss/<calendarid>'=>array('/export/rss', 'urlSuffix'=>'.rss'),
				//'export/ical/<calendarid>/<id>'=>array('/export/ical', 'urlSuffix'=>'.ical'),
				
				// Login Routes
				'<calendarid>/<action:(login|logout)>'=>'/auth/<action>',
				'<calendarid>/login/<action:\w+>'=>'/auth/<action>',
				'<action:(login|logout)>'=>'/auth/<action>',
				'login/<action:\w+>'=>'/auth/<action>',
				
				// Short 'events' action routes.
				'<calendarid>/<action:(day|week|month)>/<date:[0-9\-]+>'=>'/events/<action>',
				'<calendarid>/<action:(upcoming|search|day|week|month)>'=>'/events/<action>',
				'<calendarid>/event/<id>'=>'/events/detail',
				'<calendarid>' => 'events',
		
				// Standard routes.
				//'<calendarid>/<controller:\w+>'=>'<controller>',
				//'<calendarid>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		
				//'<controller:\w+>/<id:\d+>'=>'<controller>/events',
				//'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				//'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'authManager'=>array(
			'class'=>'DbAuthManager',
			'itemTable'=>'{{authitems}}',
			'assignmentTable'=>'{{authassignments}}',
			'itemChildTable'=>'{{authitemchildren}}',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),
);
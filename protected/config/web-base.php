<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

$yii = dirname(__FILE__).'/../../framework/yii.php';

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'name'=>'UWebCal',
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'defaultController'=>'view',
	
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'timezone'=>'UTC',
		'defaultCalendarId'=>'default',
		'defaultViewAction'=>'upcoming',
		'calendarNotFoundRedirect'=>null,
	),
	
	// components to preload
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.auth.*',
		'application.components.auth.rbac.*',
	),

	'modules'=>array(
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				// view/index routes.
				'' => 'view',
				'<calendarid>' => 'view',
		
				// Short 'view' action routes.
				'<calendarid>/<action:(upcoming|search|day|week|month)>'=>'/view/<action>',
				'<calendarid>/<action:(day|week|month)>/<date:[0-9\-]+>'=>'/view/<action>',
				'<calendarid>/event/<id>'=>'/view/event',
		
				'/export/rss/<calendarid>'=>array('/export/rss', 'urlSuffix'=>'.rss'),
				'/export/ical/<calendarid>/<id>'=>array('/export/ical', 'urlSuffix'=>'.ical'),
				
				// Standard routes.
				'<calendarid>/<controller:\w+>'=>'<controller>',
				'<calendarid>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		
				//'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				//'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				//'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'authManager'=>array(
			'class'=>'DbAuthManager'
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
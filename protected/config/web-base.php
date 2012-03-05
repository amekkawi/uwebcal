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
		'authActions'=>array(),
		'userIdentities'=>array('DbUserIdentity')
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
			'showScriptName'=>false,
			'rules'=>array(
				'<calendarid>/<action:(login|logout)>'=>'/auth/<action>',
				'<calendarid>/login/<action:\w+>'=>'/auth/<action>',
				'<action:(login|logout)>'=>'/auth/<action>',
				'login/<action:\w+>'=>'/auth/<action>',
				
				'<calendarid>' => 'view',
				
				// Short 'view' action routes.
				'<calendarid>/<action:(upcoming|search|day|week|month)>'=>'/view/<action>',
				'<calendarid>/<action:(day|week|month)>/<date:[0-9\-]+>'=>'/view/<action>',
				'<calendarid>/event/<id>'=>'/view/event',
		
				'export/rss/<calendarid>'=>array('/export/rss', 'urlSuffix'=>'.rss'),
				'export/ical/<calendarid>/<id>'=>array('/export/ical', 'urlSuffix'=>'.ical'),
				
				// Standard routes.
				'<calendarid>/<controller:\w+>'=>'<controller>',
				'<calendarid>/<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
		
				//'<controller:\w+>/<id:\d+>'=>'<controller>/view',
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
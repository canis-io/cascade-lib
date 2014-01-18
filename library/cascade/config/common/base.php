<?php
return [
	'id' => 'cascade',
	'name' => 'Cascade',
	'basePath' => INFINITE_CASCADE_PATH,
	'vendorPath' => INFINITE_APP_INSTALL_PATH . DIRECTORY_SEPARATOR . 'vendor',
	'runtimePath' => INFINITE_APP_INSTALL_PATH . DIRECTORY_SEPARATOR . 'runtime',
	'preload' => ['log', 'collectors'],
	'language' => 'en',
	'modules' => include(INFINITE_APP_ENVIRONMENT_PATH . DIRECTORY_SEPARATOR . 'modules.php'),
	'extensions' => include(INFINITE_APP_VENDOR_PATH . DIRECTORY_SEPARATOR . 'yiisoft'. DIRECTORY_SEPARATOR . 'extensions.php'),
	
	// application components
	'components' => [
		'db' => include(INFINITE_APP_ENVIRONMENT_PATH . DIRECTORY_SEPARATOR . "database.php"),
		'redis' => include(INFINITE_APP_ENVIRONMENT_PATH . DIRECTORY_SEPARATOR . 'redis.php'),
		'collectors' => include(INFINITE_APP_ENVIRONMENT_PATH . DIRECTORY_SEPARATOR . 'collectors.php'),
		'cache' => ['class' => 'yii\redis\Cache'],
		'errorHandler' => [
			'discardExistingOutput' => false
		],
		'view' => [
			'class' => 'infinite\web\View',
		],
		'response' => [
			'class' => 'infinite\web\Response'
		],
		
		'gk' => [
			'class' => 'infinite\\security\\Gatekeeper',
			'acaClass' => 'cascade\\models\\Aca',
			'aclClass' => 'cascade\\models\\Acl',
			'aclRoleClass' => 'cascade\\models\\AclRole',
			'groupClass' => 'cascade\\models\\Group',
			'registryClass' => 'cascade\\models\\Registry',
			'userClass' => 'cascade\\models\\User'
		],
		'log' => [
			'class' => 'yii\log\Logger',
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
	],
	'params' => include(INFINITE_APP_ENVIRONMENT_PATH . DIRECTORY_SEPARATOR . "params.php"),
];
?>
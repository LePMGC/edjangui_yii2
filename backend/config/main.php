<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'name' => 'eDjangui',
    'bootstrap' => ['log'],
    'modules' => [
	   'gii' => [
			 'class' => 'yii\gii\Module',
	        'allowedIPs' => ['127.0.0.1', '::1', '*.*.*.*'] // adjust this to your needs
		],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ]
	],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'session' => [
            'timeout' => 60*5, // 2 weeks, 3600 - 1 hour, Default 1440
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
	           'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
	           'authTimeout' => 300,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
         'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

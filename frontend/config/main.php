<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
       'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ]
    ],
    'name' => 'eDjangui',
    'components' => [
        'languagepicker' => [
            'class' => 'lajax\languagepicker\Component',        
            'languages' => ['en-UK', 'fr-FR'],            // List of available languages (icons only)
            'cookieName' => 'language',                         // Name of the cookie.
            'expireDays' => 64,                                 // The expiration time of the cookie is 64 days.
            'callback' => function() {
                if (!\Yii::$app->user->isGuest) {
                    $user = \Yii::$app->user->identity;
                    //$user->language = \Yii::$app->language;
                    $user->save();
                }
            }
        ],

        'device' => [
            'class' => 'xstreamka\mobiledetect\Device',
            'tablet' => ['SM-T975'], // Array of users' tablets devices.
            'phone' => [] // Array of users' phone devices.
        ],
    
        'formatter' => [
            'currencyCode' => 'XAF',
       ],

        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'session' => [
            'timeout' => 60*5, // 2 weeks, 3600 - 1 hour, Default 1440
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'authTimeout' => 300,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'tchopnow@gmail.com',
        //'password' => 'tchopnow2016',
        'password' => 'ckzpupyjqdxqzpet',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],

         'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
	'urlManager' => [
	    'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

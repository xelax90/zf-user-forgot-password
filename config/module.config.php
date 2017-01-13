<?php
namespace XelaxUserForgotPassword;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use XelaxUserForgotPassword\Service\ForgotPassword;
use XelaxUserNotification\Notification\Handler\RenderAndMailHandler;
use Zend\ServiceManager\Factory\InvokableFactory;
use XelaxUserNotification\Module as UserNotificationModule;

return [
	Module::CONFIG_KEY => [
		
	],

    'controllers' => [
        'factories' => [
            Controller\ForgotPasswordController::class => Controller\Factory\ForgotPasswordControllerFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            'zfcuser' => [
                'child_routes' => [
                    'forgot-password' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/forgot-password',
                            'defaults' => [
                                'controller' => Controller\ForgotPasswordController::class,
                                'action'     => 'request',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'reset' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/reset/:token',
                                    'defaults' => [
                                        'action'     => 'reset',
                                    ],
                                    'constraints' => [
                                        'token' => '[A-Fa-f0-9]+',
                                    ],
                                ],
                            ],
                            'finish-reset' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/finishReset',
                                    'defaults' => [
                                        'action'     => 'finishReset',
                                    ],
                                ],
                            ],
                            'finish-request' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/finishRequest',
                                    'defaults' => [
                                        'action'     => 'finishRequest',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

        ],
    ],

    UserNotificationModule::CONFIG_KEY => [
        'template_map' => [
            ForgotPassword::NOTIFICATION_TYPE_PASSWORD_RESET => [
                RenderAndMailHandler::TEMPLATE_KEY_SUBJECT => 'forgot_password.email.password_reset.subject',
                RenderAndMailHandler::TEMPLATE_KEY_HTML => 'forgot_password.email.password_reset.html',
                RenderAndMailHandler::TEMPLATE_KEY_TEXT => 'forgot_password.email.password_reset.text',
            ],
            ForgotPassword::NOTIFICATION_TYPE_REQUEST_PASSWORD => [
                RenderAndMailHandler::TEMPLATE_KEY_SUBJECT => 'forgot_password.email.password_request.subject',
                RenderAndMailHandler::TEMPLATE_KEY_HTML => 'forgot_password.email.password_request.html',
                RenderAndMailHandler::TEMPLATE_KEY_TEXT => 'forgot_password.email.password_request.text',
            ],
        ]
    ],

	'doctrine' => [
		'driver' => [
			__NAMESPACE__ . '_driver' => [
				'class' => AnnotationDriver::class, // use AnnotationDriver
				'cache' => 'array',
				'paths' => [__DIR__ . '/../src/Entity'] // entity path
			],
			'orm_default' => [
				'drivers' => [
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
				]
			]
		],
	],
	
	'translator' => [
		'translation_file_patterns' => [
			[
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
			],
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
		],
	],
	
	
	'view_manager' => [
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
];
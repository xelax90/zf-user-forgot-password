<?php
namespace XelaxUserForgotPassword;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

class Module implements ConfigProviderInterface, ServiceProviderInterface, BootstrapListenerInterface, FormElementProviderInterface
{
	const CONFIG_KEY = 'xelax_user_forgot_password';
	
	public function onBootstrap(EventInterface $e) {
		if(!$e instanceof MvcEvent){
			return;
		}
		
		$app = $e->getApplication();
		$eventManager = $app->getEventManager();
		$container = $app->getServiceManager();
		
		/* @var $notificationListener Listener\NotificationListener */
		//$notificationListener = $container->get(Listener\NotificationListener::class);
		//$notificationListener->attach($eventManager);
	}

	public function getConfig() {
		return include __DIR__ . '/../config/module.config.php';
	}

	public function getServiceConfig() {
		return [
			'factories' => [
			    Service\ForgotPassword::class => Service\Factory\ForgotPasswordFactory::class,
                Options\ForgotPasswordOptions::class => Options\Factory\ForgotPasswordOptionsFactory::class,
			],
			'delegators' => [
			],
		];
	}

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getFormElementConfig() {
        return [
            'factories' => [
                Form\ForgotPasswordForm::class => Form\Factory\ForgotPasswordFormFactory::class,
                Form\ResetPasswordForm::class => Form\Factory\ForgotPasswordFormFactory::class,
            ]
        ];
    }
}
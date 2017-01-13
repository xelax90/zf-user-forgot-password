<?php
namespace XelaxUserForgotPassword\Service\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use XelaxUserForgotPassword\Options\ForgotPasswordOptions;
use XelaxUserForgotPassword\Service\ForgotPassword;
use XelaxUserNotification\Service\Notification;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ForgotPasswordFactory implements FactoryInterface{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        $em = $container->get(EntityManager::class);
        $options = $container->get(ForgotPasswordOptions::class);
        $userMapper = $container->get('zfcuser_user_mapper');
        $passwordOptoins = $container->get('zfcuser_module_options');
        $notificationServcice = $container->get(Notification::class);
        $eventManager = $container->get('EventManager');

        return new ForgotPassword($em, $options, $userMapper, $passwordOptoins, $notificationServcice, $eventManager);
    }
}
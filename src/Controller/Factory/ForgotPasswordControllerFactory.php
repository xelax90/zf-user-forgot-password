<?php
namespace XelaxUserForgotPassword\Controller\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use XelaxUserForgotPassword\Controller\ForgotPasswordController;
use XelaxUserForgotPassword\Form\ForgotPasswordForm;
use XelaxUserForgotPassword\Form\ResetPasswordForm;
use XelaxUserForgotPassword\Service\ForgotPassword as ForgotPasswordService;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ForgotPasswordControllerFactory implements FactoryInterface{

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

        $formManager = $container->get('FormElementManager');
        $resetForm = $formManager->get(ResetPasswordForm::class);
        $forgotForm = $formManager->get(ForgotPasswordForm::class);
        $forgotService = $container->get(ForgotPasswordService::class);
        $userMapper = $container->get('zfcuser_user_mapper');

        return new ForgotPasswordController($resetForm, $forgotForm, $forgotService, $userMapper);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: schurix
 * Date: 06.01.17
 * Time: 20:40
 */

namespace XelaxUserForgotPassword\Form\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ForgotPasswordFormFactory implements FactoryInterface {

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
        if ($options === null) {
            $options = [];
        }

        if (isset($options['name'])) {
            $name = $options['name'];
        } else {
            // 'Zend\Form\Element' -> 'element'
            $parts = explode('\\', $requestedName);
            $name = strtolower(array_pop($parts));
        }

        if (isset($options['options'])) {
            $options = $options['options'];
        }

        $authOptions = $container->get('zfcuser_module_options');

        return new $requestedName($name, $options, $authOptions);
    }
}
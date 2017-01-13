<?php
namespace XelaxUserForgotPassword\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ResetPasswordForm  extends Form implements InputFilterProviderInterface {

    public function init() {
        parent::init();

        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => gettext_noop('Password'),
            ],
        ]);

        $this->add([
            'name' => 'password_verify',
            'type' => 'Password',
            'options' => [
                'label' => gettext_noop('Confirm password'),
            ],
        ]);

        $this->add([
            'type' => 'Submit',
            'attributes' => [
                'value' => gettext_noop('Change password'),
            ],
        ]);
    }


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification() {
        return [
            'password' => [
                'required' => true,
                'validators' => [
                    [ 'name' => 'StringLength', 'options' => [ 'min' => 6 ] ]
                ],
                'filters' => [
                    [ 'name' => 'StringTrim' ],
                ],
            ],
            'password_verify' => [
                'required' => true,
                'validators' => [
                    [ 'name' => 'StringLength', 'options' => [ 'min' => 6 ] ],
                    [ 'name' => 'identical', 'options' => [ 'token' => 'password' ] ],
                ],
                'filters' => [
                    [ 'name' => 'StringTrim' ],
                ],
            ],
        ];
    }
}
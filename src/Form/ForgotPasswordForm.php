<?php
namespace XelaxUserForgotPassword\Form;

use Zend\Form\Form;
use Zend\Form\View\Helper\FormSubmit;
use Zend\InputFilter\InputFilterProviderInterface;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ForgotPasswordForm extends Form implements InputFilterProviderInterface{

    /** @var  AuthenticationOptionsInterface */
    protected $authenticationOptions;

    public function __construct($name = null, array $options = [], AuthenticationOptionsInterface $authenticationOptions) {
        $this->authenticationOptions = $authenticationOptions;
        parent::__construct($name, $options);
    }

    /**
     * @return AuthenticationOptionsInterface
     */
    public function getAuthenticationOptions() {
        return $this->authenticationOptions;
    }

    public function init() {
        parent::init();

        $label = '';
        foreach ($this->getAuthenticationOptions()->getAuthIdentityFields() as $mode) {
            $label = (!empty($label) ? $label . ' or ' : '') . ucfirst($mode);
        }

        $this->add([
            'name' => 'identity',
            'type' => 'Text',
            'options' => [
                'label' => $label,
            ],
        ]);

        $this->add([
            'type' => 'Submit',
            'attributes' => [
                'value' => gettext_noop('Request password'),
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
            'identity' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
            ],
        ];
    }
}
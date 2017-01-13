<?php
namespace XelaxUserForgotPassword\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use XelaxUserForgotPassword\Form\ForgotPasswordForm;
use XelaxUserForgotPassword\Form\ResetPasswordForm;
use XelaxUserForgotPassword\Service\ForgotPassword;
use XelaxUserEntity\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Mapper\UserInterface as UserMapper;


class ForgotPasswordController extends AbstractActionController{

    /** @var  ResetPasswordForm */
    protected $resetPasswordForm;

    /** @var  ForgotPasswordForm */
    protected $forgotPasswordForm;

    /** @var  ForgotPassword */
    protected $forgotPasswordService;

    /** @var  UserMapper */
    protected $userMapper;

    /**
     * ForgotPasswordController constructor.
     * @param ResetPasswordForm $resetPasswordForm
     * @param ForgotPasswordForm $forgotPasswordForm
     */
    public function __construct(ResetPasswordForm $resetPasswordForm, ForgotPasswordForm $forgotPasswordForm, ForgotPassword $forgotPasswordService, UserMapper $userMapper) {
        $this->resetPasswordForm = $resetPasswordForm;
        $this->forgotPasswordForm = $forgotPasswordForm;
        $this->forgotPasswordService = $forgotPasswordService;
        $this->userMapper = $userMapper;
    }

    /**
     * @return ResetPasswordForm
     */
    public function getResetPasswordForm() {
        return $this->resetPasswordForm;
    }

    /**
     * @return ForgotPasswordForm
     */
    public function getForgotPasswordForm() {
        return $this->forgotPasswordForm;
    }

    /**
     * @return ForgotPassword
     */
    public function getForgotPasswordService() {
        return $this->forgotPasswordService;
    }

    /**
     * @return UserMapper
     */
    public function getUserMapper() {
        return $this->userMapper;
    }

    protected function _redirectToProfile(){
        return $this->redirect()->toRoute('zfcuser');
    }

    protected function _redirectToFinishRequest(){
        return $this->redirect()->toRoute('zfcuser/forgot-password/finish-request');
    }

    protected function _redirectToFinishReset(){
        return $this->redirect()->toRoute('zfcuser/forgot-password/finish-reset');
    }

    public function requestAction() {
        if($this->zfcUserAuthentication()->hasIdentity()){
            return $this->_redirectToProfile();
        }

        $form = $this->getForgotPasswordForm();
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();

        $data = ['form' => $form];

        if(!$request->isPost()){
            return $data;
        }

        $form->setData($request->getPost());

        if(!$form->isValid()){
            return $data;
        }

        $formData = $form->getData();

        $user = $this->getUserMapper()->findByEmail($formData['identity']);
        if(!$user){
            $data['error'] = gettext_noop('User not found');
            return $data;
        }

        $this->getForgotPasswordService()->requestPasswordReset($user);

        return $this->_redirectToFinishRequest();
    }

    public function finishRequestAction(){
        return new ViewModel();
    }

    public function resetAction() {
        $id = $this->getEvent()->getRouteMatch()->getParam('token');
        if(!$this->getForgotPasswordService()->passwordKeyExists($id)){
            return ['error' => gettext_noop('Password key not found')];
        }

        $form = $this->getResetPasswordForm();
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();
        $user = $this->getForgotPasswordService()->getUserForPasswordReset($id);

        $data = ['form' => $form, 'user' => $user, 'token' => $id];

        if(!$request->isPost()){
            return $data;
        }

        $form->setData($request->getPost());

        if(!$form->isValid()){
            return $data;
        }

        $formData = $form->getData();

        $newPassword = $formData['password'];
        $success = $this->getForgotPasswordService()->resetPassword($id, $newPassword);
        if($success){
            return $this->_redirectToFinishReset();
        }

        $data['error'] = gettext_noop('An error occured');
        return $data;
    }

    public function finishResetAction(){
        return new ViewModel();
    }
}
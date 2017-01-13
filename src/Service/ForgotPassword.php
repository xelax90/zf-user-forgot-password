<?php
namespace XelaxUserForgotPassword\Service;

use XelaxUserEntity\Entity\User;
use XelaxUserForgotPassword\Model\ForgotPasswordRepository;
use XelaxUserForgotPassword\Options\ForgotPasswordOptions;
use XelaxUserForgotPassword\Entity\ForgotPassword as ForgotPasswordEntity;
use Doctrine\ORM\EntityManager;
use ZfcUser\Service\User as UserService;
use ZfcUserDoctrineORM\Mapper\User as UserMapper;
use XelaxUserEntity\Entity\User as UserEntity;
use XelaxUserNotification\Service\Notification as NotificationService;
use Zend\EventManager\EventManagerInterface;
use ZfcUser\Options\PasswordOptionsInterface;
use Zend\Crypt\Password\Bcrypt;

/**
 * Description of ForgotPassword
 *
 * @author schurix
 */
class ForgotPassword {
	
	const NOTIFICATION_TYPE_REQUEST_PASSWORD = 'forgot_password_request';
    const NOTIFICATION_TYPE_PASSWORD_RESET = 'forgot_password_reset';
    const EVENT_RESET_PASSWORD = 'resetPassword';
	
	/** @var EntityManager */
	protected $entityManager;
	
	/** @var ForgotPasswordOptions */
	protected $options;
	
	/** @var UserMapper */
	protected $userMapper;
	
	/** @var PasswordOptionsInterface */
	protected $passwordOptions;
	
	/** @var NotificationService */
	protected $notificationService;

	/** @var  EventManagerInterface */
	protected $eventManager;

	/** @var bool */
	protected $clearedExpired = false;

    /**
     * ForgotPassword constructor.
     * @param EntityManager $entityManager
     * @param ForgotPasswordOptions $options
     * @param UserMapper $userMapper
     * @param UserService $userService
     * @param NotificationService $notificationService
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EntityManager $entityManager, ForgotPasswordOptions $options, UserMapper $userMapper, PasswordOptionsInterface $passwordOptions, NotificationService $notificationService, EventManagerInterface $eventManager) {
        $this->entityManager = $entityManager;
        $this->options = $options;
        $this->userMapper = $userMapper;
        $this->passwordOptions = $passwordOptions;
        $this->notificationService = $notificationService;
        $this->setEventManager($eventManager);
    }


    /**
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }

    /**
     * @return ForgotPasswordOptions
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return UserMapper
     */
    public function getUserMapper() {
        return $this->userMapper;
    }

    /**
     * @return PasswordOptionsInterface
     */
    public function getPasswordOptions() {
        return $this->passwordOptions;
    }

    /**
     * @return NotificationService
     */
    public function getNotificationService() {
        return $this->notificationService;
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager() {
        return $this->eventManager;
    }

    /**
     * @param EventManagerInterface $eventManager
     * @return ForgotPassword
     */
    public function setEventManager(EventManagerInterface $eventManager) {
        $identifiers = [__CLASS__, static::class];
        $eventManager->setIdentifiers($identifiers);
        $this->eventManager = $eventManager;
        return $this;
    }

    protected function clearExpiredRequests($force = false){
        if($force || !$this->clearedExpired){
            $this->getEntityManager()->getRepository(ForgotPasswordEntity::class)
                ->clearExpiredRequests($this->getOptions()->getRequestLifetime());
            $this->clearedExpired = true;
        }
    }

	public function requestPasswordReset(UserEntity $user){
        $existingPasswords = $this->getEntityManager()->getRepository(ForgotPasswordEntity::class)->findBy(['user' => $user]);
        foreach($existingPasswords as $existingPassword) {
            $this->getEntityManager()->remove($existingPassword);
        }

        $password = new ForgotPasswordEntity();
        $password->setUser($user);
        $password->generateId();

        $this->getEntityManager()->persist($password);
        $this->getEntityManager()->flush();

        $this->getNotificationService()->sendSystemNotification(static::NOTIFICATION_TYPE_REQUEST_PASSWORD, $user, ['passwordEntity' => $password]);
    }

    public function resetPassword($forgotPasswordKey, $newPassword){
	    // force clear to be sure
        $this->clearExpiredRequests(true);

        /* @var $entity ForgotPasswordEntity */
	    $entity = $this->getEntityManager()->getRepository(ForgotPasswordEntity::class)->find($forgotPasswordKey);
	    if(!$entity){
	        return false;
        }
	    return $this->resetPasswordWithEntity($entity, $newPassword);
    }

    public function passwordKeyExists($forgotPasswordKey) {
        $this->clearExpiredRequests();

        $entity = $this->getEntityManager()->getRepository(ForgotPasswordEntity::class)->find($forgotPasswordKey);
        return !!$entity;
    }

    public function getUserForPasswordReset($forgotPasswordKey) {
        $this->clearExpiredRequests();

        /* @var $entity ForgotPasswordEntity */
        $entity = $this->getEntityManager()->getRepository(ForgotPasswordEntity::class)->find($forgotPasswordKey);
        if(!$entity){
            return null;
        }
        return $entity->getUser();
    }

	protected function resetPasswordWithEntity(ForgotPasswordEntity $forgotPassword, $newPassword){
        /* @var $currentUser User */
        $currentUser = $forgotPassword->getUser();

        $bcrypt = new Bcrypt;
        $bcrypt->setCost($this->getPasswordOptions()->getPasswordCost());

        $pass = $bcrypt->create($newPassword);
        $currentUser->setPassword($pass);

        $this->getEventManager()->trigger(static::EVENT_RESET_PASSWORD, $this, ['forgotPassword' => $forgotPassword, 'newCredential' => $newPassword]);
        $this->getUserMapper()->update($currentUser);
        $this->getEventManager()->trigger(static::EVENT_RESET_PASSWORD.'.post', $this, ['forgotPassword' => $forgotPassword, 'newCredential' => $newPassword]);

        $this->getNotificationService()->sendSystemNotification(static::NOTIFICATION_TYPE_PASSWORD_RESET, $currentUser, ['passwordEntity' => $forgotPassword, 'newPassword' => $newPassword]);

        $this->getEntityManager()->remove($forgotPassword);
        $this->getEntityManager()->flush($forgotPassword);
        return true;
	}
}

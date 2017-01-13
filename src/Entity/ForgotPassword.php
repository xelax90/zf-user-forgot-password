<?php
namespace XelaxUserForgotPassword\Entity;

use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;
use DateTime;
/**
 * Stores password recovery requests
 *
 * @author schurix
 * @ORM\Entity(repositoryClass="\XelaxUserForgotPassword\Model\ForgotPasswordRepository")
 * @ORM\Table(name="user_forgot_password")
 * @ORM\HasLifecycleCallbacks
 */
class ForgotPassword {
	
	const ID_LENGTH = 64;
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=ForgotPassword::ID_LENGTH)
	 * @var string
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="\XelaxUserEntity\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
	 * @var UserInterface
	 */
	protected $user;
	
	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @var DateTime
	 */
	protected $createdAt;
	
	public function getId() {
		return $this->id;
	}

	public function getUser() {
		return $this->user;
	}

	public function getCreatedAt() {
		return $this->createdAt;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setUser(UserInterface $user) {
		$this->user = $user;
		return $this;
	}

	public function setCreatedAt(DateTime $createdAt) {
		$this->createdAt = $createdAt;
		return $this;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function preCreate(){
		$this->createdAt = new DateTime();
	}
	
	public function generateId(){
		$this->id = bin2hex(openssl_random_pseudo_bytes(self::ID_LENGTH / 2));
		return $this->id;
	}

	function __toString() {
        return 'id: '.$this->id.PHP_EOL.'user: '.$this->user->getDisplayName();
    }
}

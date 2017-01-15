<?php
namespace XelaxUserForgotPassword\Options;

use Zend\Form\Element\DateTime;
use Zend\Stdlib\AbstractOptions;
use DateInterval;
use Zend\Stdlib\Exception\InvalidArgumentException;

/**
 * Description of ForgotPasswordOptions
 *
 * @author schurix
 */
class ForgotPasswordOptions extends AbstractOptions{
	
	/**
	 * @var DateInterval
	 */
	protected $requestLifetime = "P1D";
	
	public function __construct($options = null) {
		parent::__construct($options);
		
		if(is_string($this->requestLifetime)){
			$this->setRequestLifetime($this->requestLifetime);
		}
	}
	
	/**
	 * @return DateInterval
	 */
	public function getRequestLifetime() {
		return $this->requestLifetime;
	}

	/**
	 * 
	 * @param DateInterval|string $requestLifetime
	 * @return self
	 * @throws InvalidArgumentException
	 */
	public function setRequestLifetime($requestLifetime) {
		if(is_string($requestLifetime)){
		    $requestLifetime = new DateInterval($requestLifetime);
		}
		if(! ($requestLifetime instanceof DateInterval) ){
			throw new InvalidArgumentException('Request Lifetime must be a DateInterval instance or valid string for its constructor.');
		}
		$this->requestLifetime = $requestLifetime;
		return $this;
	}
}

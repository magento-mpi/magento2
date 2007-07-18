<?php
/**
 * Subscriber model
 *
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
class Mage_Newsletter_Model_Subscriber extends Varien_Object
{
    /**
     * Status unsubscribed
     * @var int
     */
    const STATUS_UNSUBSCRIBED = 3;
    
    /**
     * Status subscribed
     * @var int
     */
    const STATUS_SUBSCRIBED = 1;
    
    /**
     * Status not activated
     * @var int
     */
    const STATUS_NOT_ACTIVE = 2;
    
    protected $_isStatusChanged = false;
    
    /**
     * Alias for getSubscriberId()
     *
     * @return int
     */
    public function getId() 
    {
        return $this->getSubscriberId();
    }
    
    /**
     * Alias for setSubscriberId()
     * 
     * @param int $value
     */
    public function setId($value)
    {
        return $this->setSubscriberId($value);
    }
    
    /**
     * Alias for getSubscriberConfirmCode()
     *
     * @return string
     */     
    public function getCode()
    {
        return $this->getSubscriberConfirmCode();
    }
    
    /**
     * Return link for confirmation of subscription
     *
     * @return string
     */
    public function getConfirmationLink() {
    	return Mage::getUrl('newsletter/subscriber/confirm',
    						array('id'=>$this->getId(),
    							  'code'=>$this->getCode()));
    }
    
    /**
     * Alias for setSubscriberConfirmCode()
     *
     * @param string $value
     */
    public function setCode($value)
    {
        return $this->setSubscriberConfirmCode($value);
    }
    
    /**
     * Alias for getSubscriberStatus()
     *
     * @return int Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED
     *             |Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
     *             |Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE
     */
    public function getStatus() 
    {
        return $this->getSubscriberStatus();
    }
    
    /**
     * Alias for setSubscriberStatus()
     *
     * @param int $value Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED
     *                   |Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
     *                   |Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE
     */
    public function setStatus($value)
    {
        return $this->setSubscriberStatus($value);
    }
    
    /**
     * Set the error messages scope for subscription
     *
     * @param boolean $scope
     * @return unknown
     */
    
    public function setMessagesScope($scope)
    {
        $this->getResource()->setMessagesScope($scope);
        return $this;
    }
    
    /**
     * Alias for getSubscriberEmail()
     *
     * @return string
     */
    public function getEmail() 
    {
        return $this->getSubscriberEmail();
    }
    
    /**
     * Alias for setSubscriberEmail()
     *
     * @param string $value
     */
    public function setEmail($value)
    {
        return $this->setSubscriberEmail($value);
    }
    
    /**
     * Set for status change flag
     *
     * @param boolean $value
     */
    public function setIsStatusChanged($value) 
    {
    	$this->_isStatusChanged = (boolean) $value;
   		return $this;
    }

    /**
     * Return status change flag value
     *
     * @return boolean
     */
    public function getIsStatusChanged()
    {
    	return $this->_isStatusChanged;
    }
    
    public function isSubscribed() 
    {
    	if($this->getId() && $this->getStatus()==self::STATUS_SUBSCRIBED) {
    		return true;
    	}
    	
    	return false;	
    }
    
    /**
     * Return resource model
     *
     * @return Mage_Subscriber_Model_Mysql4_Subscriber
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('newsletter/subscriber');
    }
    
    /**
     * Load subscriber data from resource model
     *
     * @param int $subscriberId
     */
    public function load($subscriberId) 
    {
        $this->addData($this->getResource()->load($subscriberId));
        return $this;
    }
    
     /**
     * Load subscriber data from resource model by email
     *
     * @param int $subscriberId
     */
    public function loadByEmail($subscriberEmail) 
    {
        $this->addData($this->getResource()->loadByEmail($subscriberEmail));
        return $this;
    }
    
    /**
     * Load subscriber info by customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function loadByCustomer(Mage_Customer_Model_Customer $customer) 
    {
        $this->addData($this->getResource()->loadByCustomer($customer));
        return $this;
    }
    
    /**
     * Save subscriber data to resource model
     *
     */
    public function save()
    {
        return $this->getResource()->save($this);
    }
    
    /**
     * Deletes subscriber data
     */
    public function delete()
    {
        $this->getResource()->delete($this->getId);
        $this->setId(null);
    }
    
    /**
     * Confirms subscriber newsletter
     *
     * @param string $code
     * @return boolean
     */
    public function confirm($code) 
    {
    	if($this->getCode()==$code) {
    		$this->setStatus(self::STATUS_SUBSCRIBED)
    			->setCode(null)
    			->setIsStatusChanged(true)
    			->save();
    		return true;
    	}
    	    	
    	return false;
    }
    
    /**
     * Mark receiving subscriber of queue newsletter
     *
     * @param  Mage_Newsletter_Model_Queue $queue
     * @return boolean
     */
    public function received(Mage_Newsletter_Model_Queue $queue) 
    {
    	$this->getResource()->received($this,$queue);
    	return $this;
    }
}
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
    const STATUS_UNSUBSCRIBED = 0;
    
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
        $this->setId('');
    }
}
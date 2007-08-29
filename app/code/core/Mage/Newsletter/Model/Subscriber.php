<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Subscriber model
 *
 * @category   Mage
 * @package    Mage_Newsletter
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

    public function getUnsubscriptionLink() {
    	return Mage::getUrl('newsletter/subscriber/unsubscribe',
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

    public function randomSequence($length=32)
    {
        $id = '';
        $par = array();
        $char = array_merge(range('a','z'),range(0,9));
        $charLen = count($char)-1;
        for ($i=0;$i<$length;$i++){
            $disc = mt_rand(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id.$char[$disc];
        }
        return $id;
    }

    public function subscribe($email)
    {
    	$this->loadByEmail($email);

    	$isNewSubscriber = false;

    	if (!$this->getSubscriberId()) {
    		if (Mage::getStoreConfig('newsletter/subscription/confirm')) {
    			$this->setSubscriberStatus(self::STATUS_NOT_ACTIVE);
    		} else {
    			$this->setSubscriberStatus(self::STATUS_SUBSCRIBED);
    		}
			$this->setSubscriberConfirmCode($this->randomSequence());
    		$this->setSubscriberEmail($email);
            $isNewSubscriber = true;
    	}

    	$customerSession = Mage::getSingleton('customer/session');

        if ($customerSession->isLoggedIn()) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
        } else {
            $this->setStoreId(Mage::getSingleton('core/store')->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
        	$this->save();
        	if ($isNewSubscriber) {
	        	if (Mage::getStoreConfig('newsletter/subscription/confirm')) {
	        		$this->sendConfirmationRequestEmail();
	        	} else {
	        		$this->sendConfirmationSuccessEmail();
	        	}
        	}
        	return $this->getSubscriberStatus();
        } catch (Exception $e) {
        	throw new Exception($e->getMessage());
        }
    }

    public function unsubscribe($email)
    {
    	try {
    		$this->setSubscriptionStatus(self::STATUS_UNSUBSCRIBED)->save();
    		return true;
    	} catch (Exception $e) {
    		return $e;
    	}
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

    public function sendConfirmationRequestEmail()
    {
    	Mage::getModel('core/email_template')
    		->sendTransactional(
    		    Mage::getStoreConfig('newsletter/subscription/confirm_email_template'),
    		    Mage::getStoreConfig('newsletter/subscription/confirm_email_identity'),
    		    $this->getEmail(),
    		    $this->getName(),
    		    array('subscriber'=>$this));
    	return $this;
    }

    public function sendConfirmationSuccessEmail()
    {
    	Mage::getModel('core/email_template')
    		->sendTransactional(
    		    Mage::getStoreConfig('newsletter/subscription/success_email_identity'),
    		    Mage::getStoreConfig('newsletter/subscription/success_email_template'),
    		    $this->getEmail(),
    		    $this->getName(),
    		    array('subscriber'=>$this));
    	return $this;
    }
}
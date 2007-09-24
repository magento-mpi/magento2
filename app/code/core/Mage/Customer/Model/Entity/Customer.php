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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer entity resource model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Moshe Gurvich <moshe@varien.com>
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Customer_Model_Entity_Customer extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('customer')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }

    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);
        /* @var $customer Mage_Customer_Model_Customer */
        $customer->setParentId(null);

//        $testCustomer = clone $customer;
//        $this->loadByEmail($testCustomer, $customer->getEmail(), true);
//
//        if ($testCustomer->getId() && $testCustomer->getId()!=$customer->getId()) {
//            Mage::throwException(__('Customer email already exists'));
//        }
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('email', $customer->getEmail());
        if ($customer->getId()) {
            $collection->addAttributeToFilter('entity_id', array('neq' => $customer->getId()));
        }

        $collection->addAttributeToFilter('store_id', array('in' => $this->getSharedStoreIds()))
            ->setPage(1,1)
            ->load();

        if ($collection->getSize() > 0) {
            Mage::throwException(__('Customer email already exists'));
        }

        return $this;
    }

    /**
     * Save customer addresses and set default addresses in attributes backend
     *
     * @param   Varien_Object $customer
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $customer)
    {
        $this->_saveAddresses($customer);
		$this->_saveSubscription($customer);

        return parent::_afterSave($customer);
    }


    protected function _saveAddresses(Mage_Customer_Model_Customer $customer)
    {
        foreach ($customer->getAddressCollection() as $address)
        {
            if ($address->getData('_deleted')) {
                $address->delete();
            }
            else {
                $address->setParentId($customer->getId())
                	->setStoreId($customer->getStoreId())
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Saves customers subscription
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return boolean
     */
    protected function _saveSubscription(Mage_Customer_Model_Customer $customer) {

    	$subscriber = Mage::getModel('newsletter/subscriber')
    		->loadByCustomer($customer);

    	if (!$customer->getIsSubscribed() && !$subscriber->getId()) {
    		// If subscription flag not seted or customer not subscriber
    		// and no subscribe bellow
    		return false;
    	}

    	if($customer->hasIsSubscribed()) {
    	    $status = ( $customer->getIsSubscribed()
    			    ? Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
    			    : Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
    	} else {
    	    $status = $subscriber->getStatus();
    	}


    	if($status != $subscriber->getStatus()) {
    		$subscriber->setIsStatusChanged(true);
    	}

		$subscriber->setStatus($status);

		if ($subscriber->getIsStatusChanged() && $status==Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
		    $subscriber->sendUnsubscriptionEmail();
		}

    	if(!$subscriber->getId()) {
    		$subscriber
    			->setStoreId($customer->getStoreId())
    			->setCustomerId($customer->getId())
    			->setEmail($customer->getEmail());
    	} else {
    	       $subscriber
    			->setEmail($customer->getEmail());
    	}

    	$subscriber->save();

    	return true;
    }


    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly=false)
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('email', $email)
            ->setPage(1,1);

        if ($testOnly) {
            $collection->addAttributeToSelect('email');
        }

        $collection->load();
        $customer->setData(array());
        foreach ($collection->getItems() as $item) {
            $customer->setData($item->getData());
            break;
        }
        return $this;
    }

    /**
     * Authenticate customer
     *
     * @param   string $email
     * @param   string $password
     * @return  false|object
     */
    public function authenticate(Mage_Customer_Model_Customer $customer, $email, $password)
    {
        $this->loadByEmail($customer, $email);
        $success = $customer->getPasswordHash()===$customer->hashPassword($password);
        if (!$success) {
            $customer->setData(array());
        }
        return $success;
    }

    /**
     * Change customer password
     * $data = array(
     *      ['password']
     *      ['confirmation']
     *      ['current_password']
     * )
     *
     * @param   Mage_Customer_Model_Customer
     * @param   array $data
     * @param   bool $checkCurrent
     * @return  this
     */
    public function changePassword(Mage_Customer_Model_Customer $customer, $newPassword, $checkCurrent=true)
    {
        $customer->setPassword($newPassword);
        $this->saveAttribute($customer, 'password_hash');
        return $this;
    }
}
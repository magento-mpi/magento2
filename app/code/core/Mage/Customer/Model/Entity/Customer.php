<?php
/**
 * Customer entity resource model
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
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
    
    /**
     * Save customer
     * 
     * @param   Varien_Object $customer
     * @return  Varien_Object
     */
    public function save(Varien_Object $customer, $loadAllAttributes=true)
    {
        $testCustomer = clone $customer;
        $this->loadByEmail($testCustomer, $customer->getEmail(), true);

        if ($testCustomer->getId() && $testCustomer->getId()!=$customer->getId()) {
            Mage::throwException('customer email already exist');
        }
        
        parent::save($customer, $loadAllAttributes);
        return $customer;
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
    
    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);
        $object->setParentId(null);
        return $this;
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
    
    	$status = ( $customer->getIsSubscribed() 
    			    ? Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED 
    			    : Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
    	
    	if($status != $subscriber->getStatus()) {
    		$subscriber->setIsStatusChanged(true);
    	}
    	
		$subscriber->setStatus($status);
		
    	if(!$subscriber->getId()) {
    		$subscriber
    			->setStoreId($customer->getStoreId())
    			->setCustomerId($customer->getId())
    			->setEmail($customer->getEmail());
    	}
    	
    	$subscriber->save();
    	
    	return true;
    }
    

    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly=false)
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->setObject($customer)
            ->addAttributeToSelect('password_hash')
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
        if ($checkCurrent) {
            /*if (empty($data['current_password'])) {
                Mage::throwException('current customer password is empty');
                //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE005'));
            }
            $testCustomer = clone $customer;
            $this->load($testCustomer, $customer->getId(), array('password_hash'));
            if ($testCustomer->getPasswordHash()!==$testCustomer->hashPassword($data['current_password'])) {
                Mage::throwException('invalid current password');
                //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE006'));
            }*/
        }
        
        /*if ($data['password'] != $data['confirmation']) {
            Mage::throwException('new passwords do not match');
            //throw Mage::exception('Mage_Customer')->addMessage(Mage::getModel('customer/message')->error('CSTE007'));
        }*/
        
        $customer->setPassword($newPassword);
        $this->saveAttribute($customer, 'password_hash');
        //->save();
        return $this;
    }
}
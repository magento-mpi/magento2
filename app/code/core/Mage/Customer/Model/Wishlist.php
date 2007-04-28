<?php
/**
 * Customer Wishlist
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Wishlist extends Varien_Data_Object
{
    public function getId()
    {
        return $this->getItemId();
    }

    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('customer_resource', 'wishlist');
        }
        return $resource;
    }
    
    public function load($itemId)
    {
        $this->setData($this->getResource()->load($itemId));
        return $this;
    }
    
    public function save()
    {
        if (!$this->getId()) {
            $this->setAddDate(new Zend_Db_Expr('NOW()'));
        }
        if (!$this->getCustomerId()) {
            $this->setCustomerId(Mage::getSingleton('customer', 'session')->getCustomerId());
        }
        if (!$this->getCustomerId()) {
            $this->setUniqCode(Mage::getSingleton('core', 'cookie')->getId());
        }
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        return $this;
    }
}

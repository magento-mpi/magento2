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
        return Mage::getSingleton('customer_resource', 'wishlist');
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
            if ($this->getResource()->loadByUniqCodeProduct($this->getUniqCode(), $this->getProductId())) {
                return $this;
            }
        } else {
            if ($this->getResource()->loadByCustomerProduct($this->getCustomerId(), $this->getProductId())) {
                return $this;
            }
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

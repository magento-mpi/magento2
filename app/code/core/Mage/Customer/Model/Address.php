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
 * Customer address
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Address extends Mage_Customer_Model_Address_Abstract
{
    protected $_customer;

    /**
     * Constructor receives $address as array of fields for new address or integer to load existing id
     *
     * @param array|integer $address
     */
    public function __construct()
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getEntityIdField());
    }
    
    /**
     * Retrieve address customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getResource()->getCustomerId($this);
    }
    
    /**
     * Declare address customer identifier
     *
     * @param unknown_type $id
     * @return unknown
     */
    public function setCustomerId($id)
    {
        $this->getResource()->setCustomerId($this, $id);
        return $this;
    }
    
    /**
     * Retrieve address customer
     *
     * @return Mage_Customer_Model_Customer | false
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }
        if (empty($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')
                ->load($this->getCustomerId());
        }
        return $this->_customer;
    }

    /**
     * Get customer address resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('customer/address');
    }

    /**
     * save customer address
     *
     * @return  Mage_Customer_Model_Address
     */
    public function save()
    {
        $this->getResource()
            ->loadAllAttributes()
            ->save($this);
        return $this;
    }

    /**
     * Delete customer address
     *
     * @return Mage_Customer_Model_Address
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        $this->setData(array());
        return $this;
    }

    /**
     * Retrieve address entity attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $attributes = $this->getResource()
                ->loadAllAttributes($this)
                ->getAttributesByCode();
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    public function __clone()
    {
        $this->setId(null);
    }
}
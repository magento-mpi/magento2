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
 * Customer address api
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Address_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrive customer addresses list
     *
     * @param int $customerId
     * @return array
     */
    public function items($customerId)
    {
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        /* @var $customer Mage_Customer_Model_Customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $result = array();
        foreach ($customer->getAddresses() as $address) {
            /* @var $address Mage_Customer_Model_Address */
            $address->setIsDefaultBilling($customer->getDefaultBillingAddress() == $address->getId());
            $address->setIsDefaultShipping($customer->getDefaultShippingAddress() == $address->getId());
            $result[] = $address->toArray();
        }

        return $result;
    }

    /**
     * Create new address for customer
     *
     * @param int $customerId
     * @param array $addressData
     * @return int
     */
    public function create($customerId, $addressData)
    {
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        /* @var $customer Mage_Customer_Model_Customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $address = Mage::getModel('customer/address')
            ->addData($addressData)
            ->setCustomerId($customer->getId());

        $valid = $address->validate();
        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $address->getId();
    }

    /**
     * Retrieve address data
     *
     * @param int $addressId
     * @return array
     */
    public function info($addressId)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        if ($customer = $address->getCustomer()) {
            $address->setIsDefaultBilling($customer->getDefaultBillingAddress() == $address->getId());
            $address->setIsDefaultShipping($customer->getDefaultShippingAddress() == $address->getId());
        }

        return $address->toArray();
    }

    /**
     * Update address data
     *
     * @param int $addressId
     * @param array $addressData
     * @return boolean
     */
    public function update($addressId, $addressData)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        $address->addData($addressData);

        $valid = $address->validate();
        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Delete address
     *
     * @param int $addressId
     * @return boolean
     */
    public function delete($addressId)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $address->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }
} // Class Mage_Customer_Model_Address_Api End
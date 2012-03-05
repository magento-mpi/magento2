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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer addresses rest
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Customer_Model_Api2_Customer_Addresses_Rest extends Mage_Customer_Model_Api2_Customer_Addresses
{
    /**
     * Create customer address
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));

        /* @var $validator Mage_Api2_Model_Resource_Validator_Eav */
        $validator = Mage::getModel('api2/resource_validator_eav', array(
            'resource' => $this,
            'operation' => self::OPERATION_CREATE
        ));

        // If the array contains more than two elements, then combine the extra elements in a string
        if (isset($data['street']) && is_array($data['street']) && count($data['street']) > 2) {
            $data['street'][1] .= Mage_Customer_Model_Api2_Customer_Addresses::STREET_SEPARATOR
                . implode(
                    Mage_Customer_Model_Api2_Customer_Addresses::STREET_SEPARATOR,
                    array_slice($data['street'], 2)
                );
            $data['street'] = array_slice($data['street'], 0, 2);
        }

        $data = $validator->filter($data);
        if (!$validator->isSatisfiedByData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::getModel('customer/address');
        $customerAddress->setData($data);
        $customerAddress->setCustomer($customer);

        try {
            $customerAddress->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($customerAddress);
    }

    /**
     * Get customer addresses list
     *
     * @return array
     */
    protected function _retrieve()
    {
        $data = array();
        /* @var $address Mage_Customer_Model_Customer */
        foreach ($this->_getCollectionForRetrieve() as $address) {
            $addressData = $address->getData();
            $addressData['street'] = $address->getStreet();
            $data[] = $addressData;
        }
        return $data;
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Customer_Model_Resource_Address_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $this->_loadCustomerById($this->getRequest()->getParam('id'));

        /* @var $collection Mage_Customer_Model_Resource_Address_Collection */
        $collection = $customer->getAddressesCollection();

        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }
}

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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer address rest
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Customer_Model_Api2_Customer_Address_Rest
    extends Mage_Customer_Model_Api2_Customer_Address
{
    /**
     * Retrieve information about specified customer address
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = $this->_loadCustomerAddressById($this->getRequest()->getParam('id'));
        return $customerAddress->getData();
    }

    /**
     * Update specified stock item
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        /* @var $validator Mage_Api2_Model_Resource_Validator */
        /*$validator = $this->_getValidator();
        if (!$validator->isSatisfiedByData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            return;
        }*/

        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = $this->_loadCustomerAddressById($this->getRequest()->getParam('id'));
        $customerAddress->addData($data);
        try {
            $customerAddress->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Delete customer
     */
    protected function _delete()
    {
        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = $this->_loadCustomerAddressById($this->getRequest()->getParam('id'));
        try {
            $customerAddress->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Load customer address by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _loadCustomerAddressById($id)
    {
        /* @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = Mage::getModel('customer/address')->load($id);
        if (!$customerAddress->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customerAddress;
    }

    /**
     * Get Validator
     *
     * @return Mage_Api2_Model_Resource_Validator
     */
    protected function _getValidator()
    {
        /* @var $validator Mage_Api2_Model_Resource_Validator */
        $validator = Mage::getModel('customer/api2_customer_address_validator_persist_factory')->create(
            Mage_Customer_Model_Api2_Customer_Address_Validator_Persist_Factory::TYPE_PERSIST_ADMIN_UPDATE
        );
        return $validator;
    }
}

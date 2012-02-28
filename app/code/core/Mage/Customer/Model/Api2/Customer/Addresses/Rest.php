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
 * API2 class for customer addresses rest
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Customer_Model_Api2_Customer_Addresses_Rest
    extends Mage_Customer_Model_Api2_Customer_Addresses
{
    /**
     * Get customer addresses list
     *
     * @return array
     */
    protected function _retrieve()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();
        return isset($data['items']) ? $data['items'] : $data;
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

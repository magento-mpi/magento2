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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for order addresses
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Api2_Order_Addresses_Rest extends Mage_Sales_Model_Api2_Order_Addresses
{
    /**
     * Retrieve order addresses
     *
     * @return array
     */
    protected function _retrieve()
    {
        $collection = $this->_getCollectionForRetrieve();

        $this->_applyCollectionModifiers($collection);
        $data = $collection->load()->toArray();

        if (0 == count($data['items'])) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }

        return $data['items'];
    }

    /**
     * Retrieve collection instances
     *
     * @return Mage_Sales_Model_Resource_Order_Address_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $collection Mage_Sales_Model_Resource_Order_Address_Collection */
        $collection = Mage::getResourceModel('sales/order_address_collection');
        $collection->addAttributeToFilter('parent_id', $this->getRequest()->getParam('order_id'));

        return $collection;
    }
}

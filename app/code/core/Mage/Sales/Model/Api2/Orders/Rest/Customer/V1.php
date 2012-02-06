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
 * API2 class for orders
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Api2_Orders_Rest_Customer_V1 extends Mage_Sales_Model_Api2_Orders_Rest
{
    /**
     * Apply owner filter (so known local ACL) for collection to avoid operations with somebody else's data
     *
     * @param Mage_Sales_Model_Resource_Collection_Abstract $collection
     * @return Mage_Sales_Model_Api2_Orders_Rest
     */
    protected function _applyLocalAclFilter(Mage_Sales_Model_Resource_Collection_Abstract $collection)
    {
        $collection->addAttributeToFilter('customer_id', array('eq' => $this->getApiUser()->getUserId()));

        return $this;
    }

    /**
     * Retrieve collection instance for orders
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = parent::_getCollectionForRetrieve();

        $this->_applyLocalAclFilter($collection);

        return $collection;
    }

    /**
     * Get location for given resource
     *
     * @param Mage_Core_Model_Abstract $product
     * @return string Location of new resource
     */
    protected function _getLocation(Mage_Core_Model_Abstract $product)
    {
        return '/';
    }

    /**
     * Get orders list
     *
     * @return array
     */
    protected function _retrieve()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();

        return isset($data['items']) ? $data['items'] : $data;
    }
}

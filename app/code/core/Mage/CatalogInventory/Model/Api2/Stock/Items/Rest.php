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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for stock items
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_CatalogInventory_Model_Api2_Stock_Items_Rest extends Mage_CatalogInventory_Model_Api2_Stock_Items
{
    /**
     * Retrieve collection instance for stock
     *
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_CatalogInventory_Model_Resource_Stock_Item_Collection */
        $collection = Mage::getResourceModel('cataloginventory/stock_item_collection');

        $this->_applyCollectionModifiers($collection);

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

    /**
     * Fetch resource type
     * Resource type should correspond to api2.xml config nodes under "config/api2/resources/"
     *
     * @return string
     */
    public function getType()
    {
        return 'stock';
    }
}

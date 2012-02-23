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
 * API2 class for stock item (admin)
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Api2_Stock_Item_Rest_Admin_V1
    extends Mage_CatalogInventory_Model_Api2_Stock_Item_Rest
{
    /**
     * Retrieve information about specified stock item
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->_loadStockItem();
        return $stockItem->getData();
    }

    /**
     * Load stock item by its id passed through request
     *
     * @throws Mage_Api2_Exception
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _loadStockItem()
    {
        $stockItemId = $this->getRequest()->getParam('id');
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->load($stockItemId);
        if (!$stockItem->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $stockItem;
    }

    /**
     * Update specified stock item
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update(array $data)
    {
        $this->_validate($data, array(), array('product_id', 'stock_id'));

        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $this->_loadStockItem();
        $stockItem->addData($data);
        try {
            $stockItem->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }
}

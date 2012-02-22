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
     * Retrieve collection instance for stock
     *
     * @return Mage_CatalogInventory_Model_Resource_Stock_Item_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $collection Mage_CatalogInventory_Model_Resource_Stock_Item_Collection */
        $collection = Mage::getResourceModel('cataloginventory/stock_item_collection');
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

    /**
     * Update specified stock items
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _update($data)
    {
        foreach ($data as $itemData) {
            $this->_updateItem($itemData);
        }
    }

    /**
     * Update specified stock item
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _updateItem($data)
    {
        try {
            $this->_saveItemOnUpdate($data);

            $this->_successMessage(
                $this->_formatMessage(self::RESOURCE_UPDATED_SUCCESSFUL, $data['item_id']),
                Mage_Api2_Model_Server::HTTP_OK
            );
        } catch (Mage_Api2_Exception $e) {
            $this->_errorMessage(
                $this->_formatMessage($e->getMessage(), $data['item_id']),
                $e->getCode()
            );
        } catch (Exception $e) {
            $this->_errorMessage(
                $this->_formatMessage($e->getMessage(), $data['item_id']),
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * Save on update specified stock item
     *
     * @param array $data
     * @throws Mage_Api2_Exception
     */
    protected function _saveItemOnUpdate($data)
    {
        if ($this->_isValid($data, array('item_id'), array('product_id', 'stock_id'))) {
            /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = $this->_loadStockItem($data['item_id']);
            $stockItem->addData($data);
            $stockItem->save();
        }
    }

    /**
     * Check is valid input data for self::create() or self::update() depends on the kind of resource:
     *   Mage_Api2_Model_Resource_Collection::create()
     *   Mage_Api2_Model_Resource_Collection::update()
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     * @return bool
     */
    protected function _isValid(array $data, array $required = array(), array $notEmpty = array())
    {
        $isValid = true;
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                $this->_errorMessage(
                    $this->_formatMessage(sprintf('Missing "%s" in request.', $key), $data['item_id']),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                );
                $isValid = false;
                continue;
            }
        }

        foreach ($notEmpty as $key) {
            if (array_key_exists($key, $data) && empty($data[$key])) {
                $this->_errorMessage(
                    $this->_formatMessage(sprintf('Empty value for "%s" in request.', $key), $data['item_id']),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                );
                $isValid = false;
                continue;
            }
        }

        return $isValid;
    }

    /**
     * Load stock item by its id
     *
     * @param int $stockItemId
     * @throws Mage_Api2_Exception
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _loadStockItem($stockItemId)
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->load($stockItemId);
        if (!$stockItem->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $stockItem;
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
}

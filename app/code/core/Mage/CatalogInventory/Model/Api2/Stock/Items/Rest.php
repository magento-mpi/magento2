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
abstract class Mage_CatalogInventory_Model_Api2_Stock_Items_Rest
    extends Mage_CatalogInventory_Model_Api2_Stock_Items
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
     */
    protected function _update(array $data)
    {
        foreach ($data as $itemData) {
            $this->_updateItem($itemData);
        }
    }

    /**
     * Update specified stock item
     *
     * @param array $data
     */
    protected function _updateItem($data)
    {
        try {
            if (!is_array($data)) {
                $this->_errorMessage(self::RESOURCE_DATA_INVALID, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
            }

            /* @var $validator Mage_CatalogInventory_Model_Api2_Stock_Item_Validator_Fields */
            $validator = Mage::getModel('cataloginventory/api2_stock_item_validator_fields', array(
                'resource' => $this
            ));

            // check idField for resource identification
            if (!$validator->idFieldIsSatisfiedByData($data)) {
                foreach ($validator->getErrors() as $error) {
                    $this->_errorMessage($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                }
                $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
            }

            /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = $this->_loadStockItemById($data[$this->getIdFieldName()]);

            // check data
            if (!$validator->isSatisfiedByData($data)) {
                foreach ($validator->getErrors() as $error) {
                    $this->_errorMessage($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
                        $data[$this->getIdFieldName()]);
                }
                $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
            }

            unset($data[$this->getIdFieldName()]); // item_id is not for update
            $stockItem->addData($data);
            $stockItem->save();

            $this->_successMessage(
                self::RESOURCE_UPDATED_SUCCESSFUL,
                Mage_Api2_Model_Server::HTTP_OK,
                $stockItem->getId()
            );
        } catch (Mage_Api2_Exception $e) {
            // pre-validation errors are already added
            if ($e->getMessage() != self::RESOURCE_DATA_PRE_VALIDATION_ERROR) {
                $this->_errorMessage(
                    $e->getMessage(),
                    $e->getCode(),
                    isset($data[$this->getIdFieldName()]) ? $data['item_id'] : null
                );
            }
        } catch (Exception $e) {
            $this->_errorMessage(
                Mage_Api2_Model_Resource::RESOURCE_INTERNAL_ERROR,
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR,
                isset($data[$this->getIdFieldName()]) ? $data[$this->getIdFieldName()] : null
            );
        }
    }

    /**
     * Load stock item by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function _loadStockItemById($id)
    {
        /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = Mage::getModel('cataloginventory/stock_item')->load($id);
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

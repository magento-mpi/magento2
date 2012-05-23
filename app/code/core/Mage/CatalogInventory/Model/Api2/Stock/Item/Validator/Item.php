<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 Stock Item Validator
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Api2_Stock_Item_Validator_Item extends Mage_Api2_Model_Resource_Validator_Fields
{
    /**
     * Validate data.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param array $data
     * @return bool
     */
    public function isValidSingleItemDataForMultiUpdate(array $data)
    {
        // Validate item id
        if (!isset($data['item_id']) || !is_numeric($data['item_id'])) {
            $this->_addError('Invalid value for "item_id" in request.');
        } else {
            // Validate Stock Item
            /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
            $stockItem = Mage::getModel('Mage_CatalogInventory_Model_Stock_Item')->load($data['item_id']);
            if (!$stockItem->getId()) {
                $this->_addError(sprintf('StockItem #%d not found.', $data['item_id']));
            } else {
                parent::isValidData($data);
            }
        }
        return !count($this->getErrors());
    }
}

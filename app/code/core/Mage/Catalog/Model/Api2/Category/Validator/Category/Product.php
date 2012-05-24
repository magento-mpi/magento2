<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 Category validator
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Category_Validator_Category_Product extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Check if category product relation is valid for save
     *
     * @param array $data
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    public function isValidForCreate(array $data, Mage_Catalog_Model_Category $category)
    {
        if ($category->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $this->_addError("Products cannot be assigned to the category tree root.");
            return empty($this->_errors);
        }
        if (!isset($data['product_id'])) {
            $this->_addError("The product ID must be specified.");
        // check product_id
        } else if ($this->_checkProductId($data['product_id'], $category)) {
            $assignedProducts = $category->getProductsPosition();
            if (array_key_exists($data['product_id'], $assignedProducts)) {
                $this->_addError("The product with ID {$data['product_id']} is already assigned "
                    . "to the specified category.");
            }
        }
        // check position only if product_id is valid
        if (empty($this->_errors) && isset($data['position'])
            && (!is_numeric($data['position']) || $data['position'] < 0)
        ) {
            $this->_addError("The 'position' value for the product with ID {$data['product_id']} "
                . "must be a positive integer or may not be set.");
        }
        return empty($this->_errors);
    }

    /**
     * Check if product with specified ID can be removed form sprcified category
     *
     * @param int $productId
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    public function isValidForDelete($productId, Mage_Catalog_Model_Category $category)
    {
        if ($this->_checkProductId($productId, $category)) {
            $assignedProducts = $category->getProductsPosition();
            if (!array_key_exists($productId, $assignedProducts)) {
                $this->_addError("The product cannot be unassigned from the specified category "
                    . "as it is not assigned to it.");
            }
        }
        return empty($this->_errors);
    }

    /**
     * Check if product with specified ID can be removed form sprcified category
     *
     * @param array $data
     * @param int $productId
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    public function isValidForUpdate(array $data, $productId, Mage_Catalog_Model_Category $category)
    {
        if ($this->_checkProductId($productId, $category)) {
            $assignedProducts = $category->getProductsPosition();
            if (!array_key_exists($productId, $assignedProducts)) {
                $this->_addError("The product position in the category cannot be updated "
                    . "as the product is not assigned to the category.");
            }
        }
        // check position only if product_id is valid
        if (empty($this->_errors)
            && (!isset($data['position']) ||!is_numeric($data['position']) || $data['position'] < 0)
        ) {
            $this->_addError("The 'position' value for the product with ID {$productId} "
                . "must be set and must be a positive integer.");
        }
        return empty($this->_errors);
    }

    /**
     * Check if the product is valid and visible in the specified store
     *
     * @param int $productId
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    protected function _checkProductId($productId, Mage_Catalog_Model_Category $category)
    {
        $isValid = true;
        /** @var $productHelper Mage_Catalog_Helper_Product */
        $productHelper = Mage::helper('Mage_Catalog_Helper_Product');
        $product = $productHelper->getProduct($productId, $category->getStoreId());
        if (!$product->getId()) {
            $this->_addError("The 'product_id' value is invalid or product with such ID does not exist.");
            $isValid = false;
        } else if ($category->getStoreId()) {
            // check if product belongs to current website
            $isValidWebsite = in_array($category->getStore()->getWebsiteId(), $product->getWebsiteIds());
            if (!$isValidWebsite) {
                $this->_addError("Product with the specified ID does not exist in the specified store.");
                $isValid = false;
            }
        }
        return $isValid;
    }
}

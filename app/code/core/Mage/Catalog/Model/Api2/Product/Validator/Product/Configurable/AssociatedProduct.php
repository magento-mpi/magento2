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
 * Catalog configurable product associated products resource validator
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable_AssociatedProduct
    extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Check if product can be assigned to the configurable one
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param array $data
     * @return bool
     */
    public function isValidForCreate(Mage_Catalog_Model_Product $configurable, array $data)
    {
        try {
            if ($configurable->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                $this->_critical('Product can be associated only with a configurable one.');
            }

            if (!isset($data['product_id'])) {
                $this->_critical('ID of the product to be associated must be specified.');
            }

            // get product to be assigned
            $productToBeAssigned = $this->_getProductToBeAssigned($data['product_id']);

            // validate product type
            $validProductTypesToBeAssigned = array('simple', 'virtual', 'downloadable');
            if (!in_array($productToBeAssigned->getTypeId(), $validProductTypesToBeAssigned)) {
                $this->_critical(sprintf('The product of the "%s" type cannot be assigned to the configurable '
                    . 'product.', $productToBeAssigned->getTypeId()));
            }
            // validate attribute set
            if ($configurable->getAttributeSetId() != $productToBeAssigned->getAttributeSetId()) {
                $this->_critical('The product to be associated must have the same attribute set '
                    . 'as the configurable one.');
            }
            // check if product is not assigned to the configurable one yet
            /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
            $configurableType = $configurable->getTypeInstance();
            $assignedProductsIds = $configurableType->getUsedProductIds($configurable);
            if (in_array($productToBeAssigned->getId(), $assignedProductsIds)) {
                $this->_critical('The product to be assigned is already assigned to the specified configurable one.');
            }
            // check that there is no product with the same configurable attributes' values
            // assigned to the configurable one
            $usedConfigurableAttributes = $configurableType->getUsedProductAttributes($configurable);
            $attributesInfo = array();
            $hasAllConfigurableAttributesValue = true;
            foreach ($usedConfigurableAttributes as $attribute) {
                $value = $productToBeAssigned->getData($attribute->getAttributeCode());
                if ($hasAllConfigurableAttributesValue && is_null($value)) {
                    $hasAllConfigurableAttributesValue = false;
                }
                $attributesInfo[$attribute->getId()] = $value;
            }
            if (!$hasAllConfigurableAttributesValue) {
                $this->_critical('The product to be associated must have all configurable attribute values'
                    . ' as the configurable product has.');
            }
            if ($configurableType->getProductByAttributes($attributesInfo, $configurable)) {
                $this->_critical("A product with the same configurable attributes' values is already assigned "
                    . "to the configurable one.");
            }
        } catch(Mage_Api2_Model_Resource_Validator_Exception $e) {
            $this->_addError($e->getMessage());
        }
        return $this->isValid();
    }

    /**
     * Get product to be assigned
     *
     * @param mixed $productId
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProductToBeAssigned($productId)
    {
        // check if ID or SKU is valid
        /** @var $productHelper Mage_Catalog_Helper_Product */
        $productHelper = Mage::helper('Mage_Catalog_Helper_Product');
        $productToBeAssigned = $productHelper->getProduct($productId, Mage_Core_Model_App::ADMIN_STORE_ID);
        if (!$productToBeAssigned->getId()) {
            $this->_critical('ID of the product to be assigned is invalid or product with such ID does not exist.');
        }
        return $productToBeAssigned;
    }

    /**
     * Check if the list of associated products can be retrieved
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @return bool
     */
    public function isValidForMultiGet(Mage_Catalog_Model_Product $configurable)
    {
        if ($configurable->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $this->_addError('Only configurable products can be used for retrieving the list of assigned products.');
        }
        return $this->isValid();
    }

    /**
     * Check if the specified associated product can be unassigned from the configurable one
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param Mage_Catalog_Model_Product $associated
     * @return bool
     */
    public function isValidForDelete(Mage_Catalog_Model_Product $configurable, Mage_Catalog_Model_Product $associated)
    {
        try {
            if ($configurable->getTypeId() != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                $this->_critical('Only configurable products can be used to unassign an associated product from.');
            }
            if (!$associated->getId()) {
                $this->_critical('ID of the product to be unassigned is invalid or product with such ID '
                    . 'does not exist.');
            }
            // check if product is assigned to the configurable one
            /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
            $configurableType = $configurable->getTypeInstance();
            $assignedProductsIds = $configurableType->getUsedProductIds($configurable);
            if (!in_array($associated->getId(), $assignedProductsIds)) {
                $this->_critical('The specified product cannot be unassigned from the configurable one '
                    . 'as it is not assigned to it.');
            }
        } catch(Mage_Api2_Model_Resource_Validator_Exception $e) {
            $this->_addError($e->getMessage());
        }
        return $this->isValid();
    }
}

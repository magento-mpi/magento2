<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Configurable Product Attribute Model
 *
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute getResource()
 * @method int getProductId()
 * @method Mage_Catalog_Model_Product_Type_Configurable_Attribute setProductId(int $value)
 * @method int getAttributeId()
 * @method Mage_Catalog_Model_Product_Type_Configurable_Attribute setAttributeId(int $value)
 * @method int getPosition()
 * @method Mage_Catalog_Model_Product_Type_Configurable_Attribute setPosition(int $value)
 *
 * @method Mage_Catalog_Model_Product_Type_Configurable_Attribute setProductAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $value)
 * @method Mage_Eav_Model_Entity_Attribute_Abstract getProductAttribute()
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable_Attribute extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute');
    }

    /**
     * Add price data to attribute
     *
     * @param array $priceData
     * @return Mage_Catalog_Model_Product_Type_Configurable_Attribute
     */
    public function addPrice($priceData)
    {
        $data = $this->getPrices();
        if (is_null($data)) {
            $data = array();
        }
        $data[] = $priceData;
        $this->setPrices($data);
        return $this;
    }

    /**
     * Retrieve attribute label
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->getData('use_default') && $this->getProductAttribute()) {
            return $this->getProductAttribute()->getStoreLabel();
        } else if (is_null($this->getData('label')) && $this->getProductAttribute()) {
            $this->setData('label', $this->getProductAttribute()->getStoreLabel());
        }

        return $this->getData('label');
    }

    /**
     * After save process
     *
     * @return Mage_Catalog_Model_Product_Type_Configurable_Attribute
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        $this->_getResource()->saveLabel($this);
        $this->_getResource()->savePrices($this);
        return $this;
    }

    /**
     * Load counfigurable attribute by product and product's attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Eav_Model_Attribute  $attribute
     */
    public function loadByProductAndAttribute($product, $attribute)
    {
        $id = $this->_getResource()->getIdByProductIdAndAttributeId($this, $product->getId(), $attribute->getId());
        if ($id) {
            $this->load($id);
        }
    }

    /**
     * Delete configurable attributes by product id
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function deleteByProduct($product)
    {
        $this->_getResource()->deleteAttributesByProductId($product->getId());
    }
}

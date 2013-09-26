<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Item option model
 *
 * @method Magento_Sales_Model_Resource_Quote_Item_Option _getResource()
 * @method Magento_Sales_Model_Resource_Quote_Item_Option getResource()
 * @method int getItemId()
 * @method Magento_Sales_Model_Quote_Item_Option setItemId(int $value)
 * @method int getProductId()
 * @method Magento_Sales_Model_Quote_Item_Option setProductId(int $value)
 * @method string getCode()
 * @method Magento_Sales_Model_Quote_Item_Option setCode(string $value)
 * @method Magento_Sales_Model_Quote_Item_Option setValue(string $value)
 */
class Magento_Sales_Model_Quote_Item_Option extends Magento_Core_Model_Abstract
    implements Magento_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    /**
     * @var Magento_Sales_Model_Quote_Item
     */
    protected $_item;

    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Quote_Item_Option');
    }

    /**
     * Checks that item option model has data changes
     *
     * @return boolean
     */
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /**
     * Set quote item
     *
     * @param   Magento_Sales_Model_Quote_Item $item
     * @return  Magento_Sales_Model_Quote_Item_Option
     */
    public function setItem($item)
    {
        $this->setItemId($item->getId());
        $this->_item = $item;
        return $this;
    }

    /**
     * Get option item
     *
     * @return Magento_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Set option product
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  Magento_Sales_Model_Quote_Item_Option
     */
    public function setProduct($product)
    {
        $this->setProductId($product->getId());
        $this->_product = $product;
        return $this;
    }

    /**
     * Get option product
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Get option value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_getData('value');
    }

    /**
     * Initialize item identifier before save data
     *
     * @return Magento_Sales_Model_Quote_Item_Option
     */
    protected function _beforeSave()
    {
        if ($this->getItem()) {
            $this->setItemId($this->getItem()->getId());
        }
        return parent::_beforeSave();
    }

    /**
     * Clone option object
     *
     * @return Magento_Sales_Model_Quote_Item_Option
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_item = null;
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry item option model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Item_Option extends Magento_Core_Model_Abstract
    implements Magento_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    /**
     * Related gift registry item
     *
     * @var Enterprise_GiftRegistry_Model_Item
     */
    protected $_item;

    /**
     * Product related to option
     *
     * @var Magento_Catalog_Model_Product $product
     */
    protected $_product;

    /**
     * Internal constructor
     * Initializes resource model
     */
    protected function _construct()
    {
        $this->_init('Enterprise_GiftRegistry_Model_Resource_Item_Option');
    }

    /**
     * Checks if item option model has data changes
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
     * Set related gift registry item
     *
     * @param   Enterprise_GiftRegistry_Model_Item $item
     * @return  Enterprise_GiftRegistry_Model_Item_Option
     */
    public function setItem($item)
    {
        $this->setItemId($item->getId());
        $this->_item = $item;
        return $this;
    }

    /**
     * Retrieve related gift registry item
     *
     * @return Enterprise_GiftRegistry_Model_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Set product related to option
     *
     * @param   Magento_Catalog_Model_Product $product
     * @return  Enterprise_GiftRegistry_Model_Item_Option
     */
    public function setProduct($product)
    {
        if (!empty($product) && !is_null($product->getId())) {
            $this->setProductId($product->getId());
            $this->_product = $product;
        }
        return $this;
    }

    /**
     * Retrieve product related to option
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
     * Initialize item identifier before data save
     *
     * @return Enterprise_GiftRegistry_Model_Item_Option
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
     * @return Enterprise_GiftRegistry_Model_Item_Option
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_item = null;
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry item option model
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model\Item;

class Option extends \Magento\Core\Model\AbstractModel
    implements \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface
{
    /**
     * Related gift registry item
     *
     * @var \Magento\GiftRegistry\Model\Item
     */
    protected $_item;

    /**
     * Product related to option
     *
     * @var \Magento\Catalog\Model\Product $product
     */
    protected $_product;

    /**
     * Internal constructor
     * Initializes resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Resource\Item\Option');
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
     * @param   \Magento\GiftRegistry\Model\Item $item
     * @return  \Magento\GiftRegistry\Model\Item\Option
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
     * @return \Magento\GiftRegistry\Model\Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Set product related to option
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\GiftRegistry\Model\Item\Option
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
     * @return \Magento\Catalog\Model\Product
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
     * @return \Magento\GiftRegistry\Model\Item\Option
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
     * @return \Magento\GiftRegistry\Model\Item\Option
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_item = null;
        return $this;
    }
}

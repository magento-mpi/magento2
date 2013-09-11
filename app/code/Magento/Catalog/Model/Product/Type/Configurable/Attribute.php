<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Configurable Product Attribute Model
 *
 * @method \Magento\Catalog\Model\Resource\Product\Type\Configurable\Attribute _getResource()
 * @method \Magento\Catalog\Model\Resource\Product\Type\Configurable\Attribute getResource()
 * @method int getProductId()
 * @method \Magento\Catalog\Model\Product\Type\Configurable\Attribute setProductId(int $value)
 * @method int getAttributeId()
 * @method \Magento\Catalog\Model\Product\Type\Configurable\Attribute setAttributeId(int $value)
 * @method int getPosition()
 * @method \Magento\Catalog\Model\Product\Type\Configurable\Attribute setPosition(int $value)
 *
 * @method \Magento\Catalog\Model\Product\Type\Configurable\Attribute setProductAttribute(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $value)
 * @method \Magento\Eav\Model\Entity\Attribute\AbstractAttribute getProductAttribute()
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Type\Configurable;

class Attribute extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Catalog\Model\Resource\Product\Type\Configurable\Attribute');
    }

    /**
     * Add price data to attribute
     *
     * @param array $priceData
     * @return \Magento\Catalog\Model\Product\Type\Configurable\Attribute
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
     * @return \Magento\Catalog\Model\Product\Type\Configurable\Attribute
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
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Attribute  $attribute
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
     * @param \Magento\Catalog\Model\Product $product
     */
    public function deleteByProduct($product)
    {
        $this->_getResource()->deleteAttributesByProductId($product->getId());
    }
}

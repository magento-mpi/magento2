<?php
/**
 * Catalog Configurable Product Attribute Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\Type\Configurable;

use Magento\Framework\Model\Context;

/**
 * @method Attribute _getResource()
 * @method Attribute getResource()
 * @method int getProductId()
 * @method Attribute setProductId(int $value)
 * @method int getAttributeId()
 * @method Attribute setAttributeId(int $value)
 * @method int getPosition()
 * @method Attribute setPosition(int $value)
 * @method Attribute setProductAttribute(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $value)
 * @method \Magento\Eav\Model\Entity\Attribute\AbstractAttribute getProductAttribute()
 */
class Attribute extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->productFactory = $productFactory;
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute');
    }

    /**
     * Retrieve product instance by product id
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (null === $this->product) {
            if ($this->getProductId()) {
                $this->product = $this->productFactory->create()->load($this->getProductId());
            }
        }
        return $this->product;
    }

    /**
     * Add price data to attribute
     *
     * @param array $priceData
     * @return $this
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
     * @return $this
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
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute  $attribute
     * @return void
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
     * @return void
     */
    public function deleteByProduct($product)
    {
        $this->_getResource()->deleteAttributesByProductId($product->getId());
    }
}

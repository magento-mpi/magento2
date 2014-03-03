<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block;

use Magento\Catalog\Model\Product as ModelProduct;

class Product extends \Magento\View\Element\Template
{
    /**
     * @var array
     */
    protected $_finalPrice = array();

    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return ModelProduct
     */
    public function getProduct()
    {
        if (!$this->getData('product') instanceof ModelProduct) {
            if ($this->getData('product')->getProductId()) {
                $productId = $this->getData('product')->getProductId();
            }
            if ($productId) {
                $product = $this->_productFactory->create()->load($productId);
                if ($product) {
                    $this->setProduct($product);
                }
            }
        }
        return $this->getData('product');
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * @return float
     */
    public function getFinalPrice()
    {
        if (!isset($this->_finalPrice[$this->getProduct()->getId()])) {
            $this->_finalPrice[$this->getProduct()->getId()] = $this->getProduct()->getFinalPrice();
        }
        return $this->_finalPrice[$this->getProduct()->getId()];
    }

    /**
     * @param ModelProduct $product
     * @return string
     */
    public function getPriceHtml($product)
    {
        $this->setTemplate('product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
}

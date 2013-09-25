<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Catalog_Block_Product extends Magento_Core_Block_Template
{
    protected $_finalPrice = array();

    /**
     * Product factory
     *
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($coreData, $context, $data);
    }

    public function getProduct()
    {
        if (!$this->getData('product') instanceof Magento_Catalog_Model_Product) {
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

    public function getPrice()
    {
        return $this->getProduct()->getPrice();
    }

    public function getFinalPrice()
    {
        if (!isset($this->_finalPrice[$this->getProduct()->getId()])) {
            $this->_finalPrice[$this->getProduct()->getId()] = $this->getProduct()->getFinalPrice();
        }
        return $this->_finalPrice[$this->getProduct()->getId()];
    }

    public function getPriceHtml($product)
    {
        $this->setTemplate('product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
}

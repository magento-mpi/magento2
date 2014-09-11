<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products in grouped grid
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GroupedProduct\Block\Product\Grouped\AssociatedProducts;

class ListAssociatedProducts extends \Magento\Backend\Block\Template
{
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->priceCurrency = $priceCurrency;
        $this->_registry = $registry;
    }

    /**
     * Retrieve grouped products
     *
     * @return array
     */
    public function getAssociatedProducts()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_registry->registry('current_product');
        $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);
        $products = array();

        foreach ($associatedProducts as $product) {
            $products[] = array(
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'price' => $this->priceCurrency->format($product->getPrice(), false),
                'qty' => $product->getQty(),
                'position' => $product->getPosition()
            );
        }
        return $products;
    }
}

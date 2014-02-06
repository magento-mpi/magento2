<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Model\Order\Admin\Item\Plugin;

class Configurable
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public  function __construct(\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * Get item sku
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     *
     * @return string
     */
    public function aroundGetSku(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Sales\Model\Order\Item $item */
        list($item) = $arguments;

        if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $productOptions = $item->getProductOptions();
            return $productOptions['simple_sku'];
        }

        return $invocationChain->proceed($arguments);
    }

    /**
     * Get item name
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     *
     * @return string
     */
    public function aroundGetName(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Sales\Model\Order\Item $item */
        list($item) = $arguments;

        if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $productOptions = $item->getProductOptions();
            return $productOptions['simple_name'];
        }

        return $invocationChain->proceed($arguments);
    }

    /**
     * Get product id
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     *
     * @return int
     */
    public function aroundGetProductId(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Sales\Model\Order\Item $item */
        list($item) = $arguments;

        if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $productOptions     = $item->getProductOptions();
            $product = $this->productFactory->create();
            return $product->getIdBySku($productOptions['simple_sku']);
        }
        return $invocationChain->proceed($arguments);
    }
}

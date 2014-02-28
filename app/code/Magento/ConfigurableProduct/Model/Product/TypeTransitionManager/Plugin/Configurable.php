<?php
/**
 * Plugin for product type transition manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\TypeTransitionManager\Plugin;

use Magento\App\RequestInterface,
    Magento\Code\Plugin\InvocationChain;

class Configurable
{
    /**
     * Request instance
     *
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Change product type to configurable if needed
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return void
     */
    public function aroundProcessProduct(array $arguments, InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $arguments[0];
        $attributes = $this->request->getParam('attributes');
        if (!empty($attributes)) {
            $product->setTypeId(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE);
            return;
        }
        $invocationChain->proceed($arguments);
    }
}

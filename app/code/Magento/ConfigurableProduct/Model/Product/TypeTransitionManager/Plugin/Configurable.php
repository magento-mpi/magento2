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

use Magento\App\RequestInterface;

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
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Change product type to configurable if needed
     *
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcessProduct(
        \Magento\Catalog\Model\Product\TypeTransitionManager $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $attributes = $this->request->getParam('attributes');
        if (!empty($attributes)) {
            $product->setTypeId(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE);
            return;
        }
        $proceed($product);
    }
}

<?php
/**
 * Plugin for product type transition manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Product\TypeTransitionManager\Plugin;

use Magento\App\RequestInterface,
    Magento\Code\Plugin\InvocationChain;

class Downloadable
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
     * Change product type to downloadable if needed
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     */
    public function aroundProcessProduct(array $arguments, InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $arguments[0];
        $isTypeCompatible = in_array($product->getTypeId(), array(
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE,
        ));
        $hasDownloadableData = $this->request->getPost('downloadable');
        if ($isTypeCompatible && $hasDownloadableData && $product->hasIsVirtual()) {
            $product->setTypeId(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE);
            return;
        }
        $invocationChain->proceed($arguments);
    }
}

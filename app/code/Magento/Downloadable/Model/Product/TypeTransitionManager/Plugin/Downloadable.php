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

use Magento\App\RequestInterface;

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
        $proceed($product);
    }
}

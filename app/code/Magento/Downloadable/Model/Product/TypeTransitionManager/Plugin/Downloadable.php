<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\Product\TypeTransitionManager\Plugin;

use Closure;
use Magento\Framework\App\RequestInterface;

/**
 * Plugin for product type transition manager
 */
class Downloadable
{
    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
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
     * Change product type to downloadable if needed
     *
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $subject
     * @param Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcessProduct(
        \Magento\Catalog\Model\Product\TypeTransitionManager $subject,
        Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $isTypeCompatible = in_array(
            $product->getTypeId(),
            array(
                \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
                \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
                \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
            )
        );
        $downloadableData = $this->request->getPost('downloadable');
        $hasDownloadableData = false;
        if (isset($downloadableData['link'])) {
            foreach ($downloadableData['link'] as $data) {
                if (empty($data['is_delete'])) {
                    $hasDownloadableData = true;
                    break;
                }
            }
        }
        if ($isTypeCompatible && $hasDownloadableData && $product->hasIsVirtual()) {
            $product->setTypeId(\Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE);
            return;
        }
        $proceed($product);
    }
}

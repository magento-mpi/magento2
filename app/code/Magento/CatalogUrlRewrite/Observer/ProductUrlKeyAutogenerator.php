<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;

class ProductUrlKeyAutogenerator
{
    /** @var ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /**
     * @param ProductUrlPathGenerator $productUrlPathGenerator
     */
    public function __construct(ProductUrlPathGenerator $productUrlPathGenerator)
    {
        $this->productUrlPathGenerator = $productUrlPathGenerator;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function invoke(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();
        $product->setUrlKey($this->productUrlPathGenerator->generateUrlKey($product));
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\CatalogUrlRewrite\Service\V1\ProductUrlGeneratorInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRedirect\Service\V1\UrlSaveInterface;

class Observer
{
    /**
     * @var ProductUrlGeneratorInterface
     */
    protected $productUrlGenerator;

    /**
     * @var UrlSaveInterface
     */
    protected $urlSave;

    /**
     * @var CatalogUrlRewriteHelper
     */
    protected $catalogUrlRewriteHelper;

    /**
     * @param ProductUrlGeneratorInterface $productUrlGenerator
     * @param UrlSaveInterface $urlSave
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     */
    public function __construct(
        ProductUrlGeneratorInterface $productUrlGenerator,
        UrlSaveInterface $urlSave,
        CatalogUrlRewriteHelper $catalogUrlRewriteHelper
    ) {
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlSave = $urlSave;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteSaving(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        if (!$product->getUrlPath() || $product->getOrigData('url_key') != $product->getData('url_key')) {
            $product->setUrlPath($this->catalogUrlRewriteHelper->generateProductUrlKeyPath($product));
        }

        if (!$product->getData('url_key') || $product->getOrigData('url_key') != $product->getData('url_key')) {
            $this->urlSave->save($this->productUrlGenerator->generate($product));
        }
    }
}

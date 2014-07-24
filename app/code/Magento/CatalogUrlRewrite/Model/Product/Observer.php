<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\UrlSaveInterface;

class Observer
{
    /**
     * @var ProductUrlGenerator
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
     * @param ProductUrlGenerator $productUrlGenerator
     * @param UrlSaveInterface $urlSave
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     */
    public function __construct(
        ProductUrlGenerator $productUrlGenerator,
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

        // TODO: create new observer for generation and saving product url path (@TODO: UrlRewrite)
        if (!$product->getUrlPath() || $product->getOrigData('url_key') != $product->getData('url_key')) {
            $product->setUrlPath($this->catalogUrlRewriteHelper->generateProductUrlKeyPath($product));
        }

        if (!$product->getData('url_key') || $product->getOrigData('url_key') != $product->getData('url_key')) {
            $this->urlSave->save($this->productUrlGenerator->generate($product));
        }
    }
}

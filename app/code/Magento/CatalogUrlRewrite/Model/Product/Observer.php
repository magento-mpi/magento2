<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\CatalogUrlRewrite\Service\V1\ProductUrlGeneratorInterface;
use Magento\UrlRedirect\Service\V1\UrlSaveInterface;

class Observer
{
    /**
     * @var ProductUrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var \Magento\UrlRedirect\Service\V1\UrlSaveInterface
     */
    protected $urlSave;

    /**
     * @param ProductUrlGeneratorInterface $urlGenerator
     * @param UrlSaveInterface $urlSave
     */
    public function __construct(ProductUrlGeneratorInterface $urlGenerator, UrlSaveInterface $urlSave)
    {
        $this->urlGenerator = $urlGenerator;
        $this->urlSave = $urlSave;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteSaving(EventObserver $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getOrigData('url_key') != $product->getData('url_key')) {
            // TODO: fix service parameter
            $urls = $this->urlGenerator->generate($product);
            $this->urlSave->save($urls);
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\CatalogUrlRewrite\Service\V1\ProductUrlGenerator;
use Magento\UrlRedirect\Service\V1\UrlPersisterInterface;

class Observer
{
    /**
     * @var ProductUrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var \Magento\UrlRedirect\Service\V1\UrlPersisterInterface
     */
    protected $urlPersister;

    /**
     * @param ProductUrlGenerator $urlGenerator
     * @param UrlPersisterInterface $urlPersister
     */
    public function __construct(ProductUrlGenerator $urlGenerator, UrlPersisterInterface $urlPersister)
    {
        $this->urlGenerator = $urlGenerator;
        $this->urlPersister = $urlPersister;
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
            $urls = $this->urlGenerator->generate($product);
            $this->urlPersister->save($urls);
        }
    }
}

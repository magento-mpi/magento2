<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRedirect\Service\V1\StorageInterface;

class Observer
{
    /**
     * @var \Magento\CatalogUrlRewrite\Model\Product\GeneratorResolverFactory
     */
    protected $generatorResolverFactory;

    /**
     * @var \Magento\UrlRedirect\Service\V1\StorageInterface
     */
    protected $storage;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\Product\GeneratorResolverFactory $generatorResolverFactory
     * @param \Magento\UrlRedirect\Service\V1\StorageInterface $storage
     */
    public function __construct(GeneratorResolverFactory $generatorResolverFactory, StorageInterface $storage)
    {
        $this->generatorResolverFactory = $generatorResolverFactory;
        $this->storage = $storage;
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
            /** @var \Magento\CatalogUrlRewrite\Model\Product\GeneratorResolver $generatorResolver */
            $generatorResolver = $this->generatorResolverFactory->create(['product' => $product]);

            $urls = $generatorResolver->generate();
            $this->storage->save($urls);
        }
    }
}

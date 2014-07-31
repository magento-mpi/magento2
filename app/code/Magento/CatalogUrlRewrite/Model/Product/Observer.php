<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;

class Observer
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /** @var \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /**
     * @param UrlGenerator $urlGenerator
     * @param UrlPersistInterface $urlPersist
     * @param \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator $productUrlPathGenerator
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        UrlPersistInterface $urlPersist,
        \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator $productUrlPathGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->urlPersist = $urlPersist;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
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

        $isChangedWebsites = $product->getIsChangedWebsites();
        if ($product->dataHasChangedFor('url_key') || $product->getIsChangedCategories() || $isChangedWebsites) {
            if ($isChangedWebsites) {
                $this->urlPersist->deleteByEntityData(
                    [
                        UrlRewrite::ENTITY_ID => $product->getId(),
                        UrlRewrite::ENTITY_TYPE => UrlGenerator::ENTITY_TYPE_PRODUCT,
                    ]
                );
            }
            $this->urlPersist->replace($this->urlGenerator->generate($product));
        }
    }

    /**
     * Remove product urls from storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteRemoving(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();

        if ($product->getId()) {
            $this->urlPersist->deleteByEntityData(
                [
                    UrlRewrite::ENTITY_ID => $product->getId(),
                    UrlRewrite::ENTITY_TYPE => UrlGenerator::ENTITY_TYPE_PRODUCT,
                ]
            );
        }
    }
}

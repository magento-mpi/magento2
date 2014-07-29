<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
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
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @param UrlGenerator $urlGenerator
     * @param UrlPersistInterface $urlPersist
     * @param FilterFactory $filterFactory
     * @param \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator $productUrlPathGenerator
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        UrlPersistInterface $urlPersist,
        FilterFactory $filterFactory,
        \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator $productUrlPathGenerator
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->urlPersist = $urlPersist;
        $this->filterFactory = $filterFactory;
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

        if ($product->dataHasChangedFor('url_key') || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites()
        ) {
            $urls = $this->urlGenerator->generate($product);

            if ($urls) {
                $this->urlPersist->replace($urls);
            } else {
                $filter = $this->filterFactory->create(['filterData' => [
                    UrlRewrite::ENTITY_ID => $product->getId(),
                    UrlRewrite::ENTITY_TYPE => UrlGenerator::ENTITY_TYPE_PRODUCT,
                ]]);
                $this->urlPersist->deleteByFilter($filter);
            }
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
            $this->urlPersist->deleteByFilter(
                $this->filterFactory->create(['filterData' => [
                        UrlRewrite::ENTITY_ID => $product->getId(),
                        UrlRewrite::ENTITY_TYPE => UrlGenerator::ENTITY_TYPE_PRODUCT,
                    ]])
            );
        }
    }
}

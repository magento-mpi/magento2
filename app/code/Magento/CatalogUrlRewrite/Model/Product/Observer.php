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

        // TODO: create new observer for generation and saving product url path (@TODO: UrlRewrite)
        if (!$product->getUrlPath() || $product->getOrigData('url_key') != $product->getData('url_key')) {
            $product->setUrlPath($this->productUrlPathGenerator->generateUrlKey($product));
        }

        if ($product->dataHasChangedFor('url_key') || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites()
        ) {
            $urls = $this->urlGenerator->generate($product);

            $filter = $this->filterFactory->create(['filterData' => [
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => UrlGenerator::ENTITY_TYPE_PRODUCT,
            ]]);
            $this->urlPersist->deleteByFilter($filter);
            if ($urls) {
                $this->urlPersist->save($urls);
            }
        }
    }
}

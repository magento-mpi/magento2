<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator;
use Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator;
use Magento\Store\Model\Store;

class ProductUrlRewriteGenerator
{
    /**
     * Entity type code
     */
    const ENTITY_TYPE = 'product';

    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator */
    protected $currentUrlRewritesRegenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator */
    protected $categoriesUrlRewriteGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator */
    protected $canonicalUrlRewriteGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistryFactory */
    protected $categoryRegistryFactory;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistry */
    protected $categoryRegistry;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator $canonicalUrlRewriteGenerator
     * @param \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator $currentUrlRewritesRegenerator
     * @param \Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator $categoriesUrlRewriteGenerator
     * @param \Magento\CatalogUrlRewrite\Model\CategoryRegistryFactory $categoryRegistryFactory
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     */
    public function __construct(
        CanonicalUrlRewriteGenerator $canonicalUrlRewriteGenerator,
        CurrentUrlRewritesRegenerator $currentUrlRewritesRegenerator,
        CategoriesUrlRewriteGenerator $categoriesUrlRewriteGenerator,
        CategoryRegistryFactory $categoryRegistryFactory,
        StoreViewService $storeViewService
    ) {
        $this->canonicalUrlRewriteGenerator = $canonicalUrlRewriteGenerator;
        $this->currentUrlRewritesRegenerator = $currentUrlRewritesRegenerator;
        $this->categoriesUrlRewriteGenerator = $categoriesUrlRewriteGenerator;
        $this->categoryRegistryFactory = $categoryRegistryFactory;
        $this->storeViewService = $storeViewService;
    }

    /**
     * Generate product url rewrites
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate(Product $product)
    {
        $this->product = $product;
        $this->categoryRegistry = $this->categoryRegistryFactory->create($product);
        $storeId = $this->product->getStoreId();

        $urls = $this->isGlobalScope($storeId)
            ? $this->generateForGlobalScope() : $this->generateForSpecificStoreView($storeId);

        $this->product = null;
        return $urls;
    }

    /**
     * Check is global scope
     *
     * @param int|null $storeId
     * @return bool
     */
    protected function isGlobalScope($storeId)
    {
        return null === $storeId || $storeId == Store::DEFAULT_STORE_ID;
    }

    /**
     * Generate list of urls for global scope
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForGlobalScope()
    {
        $urls = [];
        foreach ($this->product->getStoreIds() as $storeId) {
            /** @TODO: UrlRewrite: check 0 == $storeId  for store view scope */
            if (!$this->storeViewService
                ->doesEntityHaveOverriddenUrlKeyForStore($storeId, $this->product->getId(), Product::ENTITY)
            ) {
                $urls = array_merge($urls, $this->generateForSpecificStoreView($storeId));
            }
        }
        return $urls;
    }

    /**
     * Generate list of urls for specific store view
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateForSpecificStoreView($storeId)
    {
        return array_merge(
            $this->canonicalUrlRewriteGenerator->generate($storeId, $this->product),
            $this->categoriesUrlRewriteGenerator->generate($storeId, $this->product, $this->categoryRegistry),
            $this->currentUrlRewritesRegenerator->generate($storeId, $this->product, $this->categoryRegistry)
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewriteGenerator;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProductUrlRewriteGenerator
{
    /**
     * Entity type code
     */
    const ENTITY_TYPE = 'product';

    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter */
    protected $converter;

    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewriteGenerator */
    protected $currentRewritesGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistryFactory */
    protected $categoryRegistryFactory;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistry */
    protected $categoryRegistry;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewriteGenerator $currentUrlRewriteGenerator
     * @param \Magento\CatalogUrlRewrite\Model\CategoryRegistryFactory $categoryRegistryFactory
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\CatalogUrlRewrite\Service\V1\StoreViewService $storeViewService
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter $converter
     */
    public function __construct(
        CurrentUrlRewriteGenerator $currentUrlRewriteGenerator,
        CategoryRegistryFactory $categoryRegistryFactory,
        ProductUrlPathGenerator $productUrlPathGenerator,
        StoreViewService $storeViewService,
        Converter $converter
    ) {
        $this->currentRewritesGenerator = $currentUrlRewriteGenerator;
        $this->categoryRegistryFactory = $categoryRegistryFactory;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->storeViewService = $storeViewService;
        $this->converter = $converter;
    }

    /**
     * Generate product url rewrites
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return UrlRewrite[]
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
            $this->generateRewritesBasedOnStoreView($storeId),
            $this->generateRewritesBasedOnCategories($storeId),
            $this->currentRewritesGenerator->generate($storeId, $this->product, $this->categoryRegistry)
        );
    }

    /**
     * Generate list based on store view
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateRewritesBasedOnStoreView($storeId)
    {
        return [
            $this->createUrlRewrite(
                $storeId,
                $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId),
                $this->productUrlPathGenerator->getCanonicalUrlPath($this->product)
            )
        ];
    }

    /**
     * Generate list based on categories
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateRewritesBasedOnCategories($storeId)
    {
        $urls = [];
        foreach ($this->categoryRegistry->getList() as $category) {
            if ($this->isCategoryProperForGenerating($category, $storeId)) {
                $urls[] = $this->createUrlRewrite(
                    $storeId,
                    $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId, $category),
                    $this->productUrlPathGenerator->getCanonicalUrlPath($this->product, $category),
                    0,
                    true,
                    $this->buildMetadataForCategory($category)
                );
            }
        }
        return $urls;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return bool
     */
    protected function isCategoryProperForGenerating($category, $storeId)
    {
        list(, $root) = $category->getParentIds();
        return $category->getParentId() != Category::TREE_ROOT_ID
            && $this->storeViewService->isRootCategoryForStore($root, $storeId);
    }

    /**
     * @param Category $category
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @return string|null
     */
    protected function buildMetadataForCategory(Category $category = null, $url = null)
    {
        $metadata = $url ? $url->getMetadata() : [];
        if ($category) {
            $metadata['category_id'] = $category->getId();
        }
        return $metadata ? serialize($metadata) : null;
    }

    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $targetPath
     * @param bool $isAutoGenerated
     * @param int $redirectType
     * @param string|null $metadata
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite(
        $storeId,
        $requestPath,
        $targetPath,
        $redirectType = 0,
        $isAutoGenerated = true,
        $metadata = null
    ) {
        return $this->converter->convertArrayToObject(
            [
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE,
                UrlRewrite::ENTITY_ID => $this->product->getId(),
                UrlRewrite::STORE_ID => $storeId,
                UrlRewrite::REQUEST_PATH => $requestPath,
                UrlRewrite::TARGET_PATH => $targetPath,
                UrlRewrite::REDIRECT_TYPE => $redirectType,
                UrlRewrite::IS_AUTOGENERATED => $isAutoGenerated,
                UrlRewrite::METADATA => $metadata,
            ]
        );
    }
}

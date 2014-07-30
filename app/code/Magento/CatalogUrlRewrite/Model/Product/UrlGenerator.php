<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;

/**
 * Product Generator
 */
class UrlGenerator
{
    /**
     * TODO: think about better place for this const (@TODO: UrlRewrite)
     *
     * Entity type
     */
    const ENTITY_TYPE_PRODUCT = 'product';

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @var UrlMatcherInterface
     */
    protected $urlMatcher;

    /**
     * @var StoreViewService
     */
    protected $storeViewService;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var Product
     */
    protected $product;

    /** @var \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /**
     * @param FilterFactory $filterFactory
     * @param UrlMatcherInterface $urlMatcher
     * @param \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator $productUrlPathGenerator
     * @param StoreViewService $storeViewService
     * @param Converter $converter
     */
    public function __construct(
        FilterFactory $filterFactory,
        UrlMatcherInterface $urlMatcher,
        \Magento\CatalogUrlRewrite\Model\Product\ProductUrlPathGenerator $productUrlPathGenerator,
        StoreViewService $storeViewService,
        Converter $converter
    ) {
        $this->filterFactory = $filterFactory;
        $this->urlMatcher = $urlMatcher;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->storeViewService = $storeViewService;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(Product $product)
    {
        $this->product = $product;
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
            if (!$this->storeViewService->doesProductHaveOverriddenUrlKeyForStore($storeId, $this->product->getId())) {
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
            $this->generateRewritesBasedOnCurrentRewrites($storeId)
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
        $collection = $this->product->getCategoryCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        foreach ($collection as $category) {
            if ($this->isCategoryProperForGenerating($category, $storeId)) {
                $urls[] = $this->createUrlRewrite(
                    $storeId,
                    $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId, $category),
                    $this->productUrlPathGenerator->getCanonicalUrlPathWithCategory($this->product, $category)
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
        return $category->getParentId() != Category::TREE_ROOT_ID && in_array($storeId, $category->getStoreIds());
    }

    /**
     * Generate list based on current rewrites
     *
     * @param int $storeId
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function generateRewritesBasedOnCurrentRewrites($storeId)
    {
        /** @var \Magento\UrlRewrite\Service\V1\Data\Filter $filter */
        $filter = $this->filterFactory->create();
        $filter->setStoreId($storeId)
            ->setEntityId($this->product->getId())
            ->setEntityType(self::ENTITY_TYPE_PRODUCT);

        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($filter) as $url) {
            $urls = array_merge(
                $urls,
                $url->getIsAutogenerated()
                    ? $this->generateForAutogenerated($url, $storeId)
                    : $this->generateForCustom($url, $storeId)
            );
        }
        return $urls;
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @param int $storeId
     * @return array
     */
    protected function generateForAutogenerated($url, $storeId)
    {
        $urls = [];
        if ($this->product->getData('save_rewrites_history')) {
            $urls[] = $this->createUrlRewrite(
                $url->getStoreId(),
                $url->getRequestPath(),
                $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId),
                OptionProvider::PERMANENT,
                false
            );
        }
        return $urls;
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @param int $storeId
     * @return array
     */
    protected function generateForCustom($url, $storeId)
    {
        $urls = [];
        $targetPath = $url->getRedirectType()
            //@TODO needs define which type should be generated(with/without category)
            ? $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId)
            : $url->getTargetPath();
        $requestPath = $url->getRequestPath();
        if ($requestPath !== $targetPath) {
            $urls[] = $this->createUrlRewrite($storeId, $requestPath, $targetPath, $url->getRedirectType(), false);
        }
        return $urls;
    }

    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $targetPath
     * @param bool $isAutoGenerated
     * @param string|null $redirectType Null or one of OptionProvider const
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite(
        $storeId,
        $requestPath,
        $targetPath,
        $redirectType = null,
        $isAutoGenerated = true
    ) {
        return $this->converter->convertArrayToObject(
            [
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE_PRODUCT,
                UrlRewrite::ENTITY_ID => $this->product->getId(),
                UrlRewrite::STORE_ID => $storeId,
                UrlRewrite::REQUEST_PATH => $requestPath,
                UrlRewrite::TARGET_PATH => $targetPath,
                UrlRewrite::REDIRECT_TYPE => $redirectType,
                UrlRewrite::IS_AUTOGENERATED => $isAutoGenerated,
            ]
        );
    }
}

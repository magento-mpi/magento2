<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;

/**
 * Category Url Generator
 */
class UrlGenerator
{
    /** Entity type @TODO: think about better place for this const (@TODO: UrlRewrite) */
    const ENTITY_TYPE_CATEGORY = 'category';

    /** @var FilterFactory */
    protected $filterFactory;

    /** @var UrlMatcherInterface */
    protected $urlMatcher;

    /** @var StoreViewService */
    protected $storeViewService;

    /** @var Converter */
    protected $converter;

    /** @var \Magento\Catalog\Model\Category */
    protected $category;

    /** @var \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /**
     * @param FilterFactory $filterFactory
     * @param UrlMatcherInterface $urlMatcher
     * @param StoreViewService $storeViewService
     * @param Converter $converter
     * @param \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(
        FilterFactory $filterFactory,
        UrlMatcherInterface $urlMatcher,
        StoreViewService $storeViewService,
        Converter $converter,
        \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator $categoryUrlPathGenerator
    ) {
        $this->filterFactory = $filterFactory;
        $this->urlMatcher = $urlMatcher;
        $this->storeViewService = $storeViewService;
        $this->converter = $converter;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($category)
    {
        $this->category = $category;
        $urls = $this->generateUrls();
        $this->category = null;
        return $urls;
    }

    /**
     * Generate list of urls for all stores assigned to category
     *
     * @return UrlRewrite[]
     */
    protected function generateUrls()
    {
        if (!$this->storeViewService->isGlobalScope($this->category->getStoreId())) {
            return $this->generatePerStore();
        }
        $categoryId = $this->category->getId();
        $urls = [];
        foreach ($this->category->getStoreIds() as $storeId) {
            if ($this->storeViewService->isGlobalScope($storeId)
                || $this->storeViewService->doesCategoryHaveOverriddenUrlKeyForStore($storeId, $categoryId)
            ) {
                continue;
            }
            $this->category->setStoreId($storeId);
            $urls = array_merge($urls, $this->generatePerStore());
        }
        return $urls;
    }

    /**
     * Generate list of urls per store
     *
     * @return UrlRewrite[]
     */
    protected function generatePerStore()
    {
        $pathIds = $this->category->getPathIds();
        if (!$this->storeViewService->isRootCategoryForStore($pathIds[1], $this->category->getStoreId())) {
            return [];
        }
        $urls[] = $this->createUrlRewrite(
            $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category),
            $this->categoryUrlPathGenerator->getCanonicalUrlPath($this->category)
        );
        $urls = array_merge(
            $urls,
            $this->generateForChildrenPerStore(),
            $this->generateCustom(),
            $this->generateRewritesHistory()
        );
        return $urls;
    }

    /**
     * Generate list of urls for children categories per store
     *
     * @return UrlRewrite[]
     */
    protected function generateForChildrenPerStore()
    {
        $childrenUrls = array();
        $category = $this->category;
        //@TODO Bug. getChildrenCategories() gets only 'is_active' categories
        foreach ($this->category->getChildrenCategories() as $childCategory) {
            $childCategory->setStoreId($category->getStoreId());
            $childCategory->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $childrenUrls = array_merge($childrenUrls, $this->generate($childCategory));
        }
        $this->category = $category;
        return $childrenUrls;
    }

    /**
     * Generate custom rewrites
     *
     * @return UrlRewrite[]
     */
    protected function generateCustom()
    {
        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($this->getFilter(0)) as $url) {
            $targetPath = $url->getRedirectType()
                ? $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category)
                : $url->getTargetPath();
            if ($url->getRequestPath() !== $targetPath) {
                $urls[] = $this->createUrlRewrite($url->getRequestPath(), $targetPath, 0, $url->getRedirectType());
            }
        }
        return $urls;
    }

    /**
     * @return UrlRewrite[]
     */
    protected function generateRewritesHistory()
    {
        $urls = [];
        if (!$this->category->getData('save_rewrites_history')) {
            return $urls;
        }
        /** @var UrlRewrite $url */
        foreach ($this->urlMatcher->findAllByFilter($this->getFilter(1)) as $url) {
            $urls[] = $this->createUrlRewrite(
                $url->getRequestPath(),
                $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category),
                0,
                OptionProvider::PERMANENT
            );
        }
        return $urls;
    }

    /**
     * @param int $isAutoGenerated
     * @return \Magento\UrlRewrite\Service\V1\Data\Filter
     */
    protected function getFilter($isAutoGenerated)
    {
        return $this->filterFactory->create()
            ->setStoreId($this->category->getStoreId())
            ->setEntityId($this->category->getId())
            ->setEntityType(self::ENTITY_TYPE_CATEGORY)
            ->setIsAutoGenerated($isAutoGenerated);
    }

    /**
     * Create url rewrite object
     *
     * @param string $requestPath
     * @param string $targetPath
     * @param int $isAutoGenerated
     * @param string|null $redirectType
     * @return UrlRewrite
     */
    protected function createUrlRewrite($requestPath, $targetPath, $isAutoGenerated = 1, $redirectType = null)
    {
        return $this->converter->convertArrayToObject(
            [
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE_CATEGORY,
                UrlRewrite::ENTITY_ID => $this->category->getId(),
                UrlRewrite::IS_AUTOGENERATED => $isAutoGenerated,
                UrlRewrite::STORE_ID => $this->category->getStoreId(),
                UrlRewrite::REQUEST_PATH => $requestPath,
                UrlRewrite::TARGET_PATH => $targetPath,
                UrlRewrite::REDIRECT_TYPE => $redirectType,
            ]
        );
    }
}

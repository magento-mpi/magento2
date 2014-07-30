<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;

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
        $urls = [];
        $storeId = $this->category->getStoreId();
        if (!$this->isGlobalScope($storeId)) {
            return $this->generatePerStore($storeId);
        }
        $categoryId = $this->category->getId();
        foreach ($this->category->getStoreIds() as $storeId) {
            if ($this->isGlobalScope($storeId)
                || $this->storeViewService->doesCategoryHaveOverriddenUrlKeyForStore($storeId, $categoryId)
            ) {
                continue;
            }
            $urls = array_merge($urls, $this->generatePerStore($storeId));
        }
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
     * Generate list of urls per store
     *
     * @param string $storeId
     * @return UrlRewrite[]
     */
    protected function generatePerStore($storeId)
    {
        $urls[] = $this->createUrlRewrite(
            $storeId,
            $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category, $storeId),
            $this->categoryUrlPathGenerator->getCanonicalUrlPath($this->category)
        );
        $urls = array_merge(
            $urls,
            $this->generateForChildrenPerStore($storeId),
            $this->generateCustomPerStore($storeId),
            $this->generatePermanentRedirectForOldUrl($storeId)
        );
        return $urls;
    }

    /**
     * Generate list of urls for children categories per store
     *
     * @param string $storeId
     * @return UrlRewrite[]
     */
    protected function generateForChildrenPerStore($storeId)
    {
        $childrenUrls = array();
        $category = $this->category;
        //@TODO BUG getChildrenCategories() returns only categories with 'is_active' == 1
        foreach ($this->category->getChildrenCategories() as $childCategory) {
            $childCategory->setStoreId($storeId);
            $childCategory->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $childrenUrls = array_merge($childrenUrls, $this->generate($childCategory));
        }
        $this->category = $category;
        return $childrenUrls;
    }

    /**
     * Generate custom rewrites
     *
     * @param $storeId
     * @return UrlRewrite[]
     */
    protected function generateCustomPerStore($storeId)
    {
        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($this->getFilter($storeId, false)) as $url) {
            $targetPath = $url->getRedirectType()
                ? $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category)
                : $url->getTargetPath();
            if ($url->getRequestPath() !== $targetPath) {
                $urls[] = $this->createUrlRewrite(
                    $storeId,
                    $url->getRequestPath(),
                    $targetPath,
                    $url->getRedirectType(),
                    false
                );
            }
        }
        return $urls;
    }

    /**
     * @param string $storeId
     * @return UrlRewrite[]
     */
    protected function generatePermanentRedirectForOldUrl($storeId)
    {
        $urls = [];
        if (!$this->category->getData('save_rewrites_history')) {
            return $urls;
        }
        foreach ($this->urlMatcher->findAllByFilter($this->getFilter($storeId)) as $url) {
            $urls[] = $this->createUrlRewrite(
                $storeId,
                $url->getRequestPath(),
                $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category),
                OptionProvider::PERMANENT,
                false
            );
        }
        return $urls;
    }

    /**
     * @param $storeId
     * @param bool $isAutoGenerated
     * @return \Magento\UrlRewrite\Service\V1\Data\Filter
     */
    protected function getFilter($storeId, $isAutoGenerated = true)
    {
        return $this->filterFactory->create()
            ->setStoreId($storeId)
            ->setEntityId($this->category->getId())
            ->setEntityType(self::ENTITY_TYPE_CATEGORY)
            ->setIsAutoGenerated($isAutoGenerated);
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
                UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE_CATEGORY,
                UrlRewrite::ENTITY_ID => $this->category->getId(),
                UrlRewrite::STORE_ID => $storeId,
                UrlRewrite::REQUEST_PATH => $requestPath,
                UrlRewrite::TARGET_PATH => $targetPath,
                UrlRewrite::REDIRECT_TYPE => $redirectType,
                UrlRewrite::IS_AUTOGENERATED => $isAutoGenerated,
            ]
        );
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\Store\Model\Store;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;

class CategoryUrlRewriteGenerator
{
    /** Entity type code */
    const ENTITY_TYPE = 'category';

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

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /**
     * @param FilterFactory $filterFactory
     * @param UrlMatcherInterface $urlMatcher
     * @param StoreViewService $storeViewService
     * @param Converter $converter
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(
        FilterFactory $filterFactory,
        UrlMatcherInterface $urlMatcher,
        StoreViewService $storeViewService,
        Converter $converter,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
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
            $this->generateRewritesBasedOnCurrentRewrites($storeId)
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
            ->setEntityId($this->category->getId())
            ->setEntityType(self::ENTITY_TYPE);

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
        if ($this->category->getData('save_rewrites_history')) {
            $targetPath = $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category, $storeId);
            if ($url->getRequestPath() !== $targetPath) {
                $urls[] = $this->createUrlRewrite(
                    $url->getStoreId(),
                    $url->getRequestPath(),
                    $targetPath,
                    OptionProvider::PERMANENT,
                    false
                );
            }
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
        $targetPath = $this->isGeneratedByUser($url) || !$url->getRedirectType()
            ? $url->getTargetPath()
            : $this->categoryUrlPathGenerator->getUrlPathWithSuffix($this->category, $storeId);

        if ($url->getRequestPath() !== $targetPath) {
            $urls[] = $this->createUrlRewrite($storeId, $url->getRequestPath(), $targetPath, $url->getRedirectType(),
                false, serialize($url->getMetadata()));
        }
        return $urls;
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @return bool
     */
    protected function isGeneratedByUser($url)
    {
        $metadata = $url->getMetadata();
        return !empty($metadata['is_user_generated']);
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
                UrlRewrite::ENTITY_ID => $this->category->getId(),
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

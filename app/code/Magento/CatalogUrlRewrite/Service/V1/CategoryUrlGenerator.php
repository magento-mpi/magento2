<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRedirect\Model\OptionProvider;
use Magento\UrlRedirect\Service\V1\Data\FilterFactory;
use Magento\UrlRedirect\Service\V1\UrlMatcherInterface;

/**
 * Product Generator
 */
class CategoryUrlGenerator implements CategoryUrlGeneratorInterface
{
    /**
     * TODO: think about better place for this const (MAGETWO-25952)
     *
     * Entity type
     */
    const ENTITY_TYPE_CATEGORY = 'category';

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlMatcherInterface
     */
    protected $urlMatcher;

    /**
     * @var CatalogUrlRewriteHelper
     */
    protected $catalogUrlRewriteHelper;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var null|\Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categories;

    /**
     * @param FilterFactory $filterFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlMatcherInterface $urlMatcher
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     */
    public function __construct(
        FilterFactory $filterFactory,
        StoreManagerInterface $storeManager,
        UrlMatcherInterface $urlMatcher,
        CatalogUrlRewriteHelper $catalogUrlRewriteHelper
    ) {
        $this->filterFactory = $filterFactory;
        $this->storeManager = $storeManager;
        $this->urlMatcher = $urlMatcher;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;
    }

    /**
     * {@inheritdoc}
     * TODO: fix service parameter (MAGETWO-25952)
     */
    public function generate($category)
    {
        $this->category = $category;
        $storeId = $this->category->getStoreId();

        $urls = $this->catalogUrlRewriteHelper->isDefaultStore($storeId)
            ? $this->generateForDefaultStore() : $this->generateForStore($storeId);

        $this->category = null;
        return $urls;
    }

    /**
     * Generate list of urls for default store
     *
     * @return UrlRewrite[]
     */
    protected function generateForDefaultStore()
    {
        $urls = [];
        foreach ($this->storeManager->getStores() as $store) {
            if (
                $this->catalogUrlRewriteHelper->isNeedCreateUrlRewrite($store->getStoreId(), $this->category->getId())
            ) {
                $urls = array_merge($urls, $this->generateForStore($store->getStoreId()));
            }
        }
        return $urls;
    }

    /**
     * Generate list of urls per store
     *
     * @param int $storeId
     * @return UrlRewrite[]
     */
    protected function generateForStore($storeId)
    {
        $urls[] = $this->createUrlRewrite(
            $storeId,
            $this->catalogUrlRewriteHelper->getCategoryUrlKeyPath($this->category),
            $this->catalogUrlRewriteHelper->getCategoryCanonicalUrlPath($this->category)
        );

        return array_merge($urls, $this->generateRewritesBasedOnCurrentRewrites($storeId));
    }

    /**
     * Generate permanent rewrites based on current rewrites
     *
     * @param int $storeId
     * @return array
     */
    protected function generateRewritesBasedOnCurrentRewrites($storeId)
    {
        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($this->createCurrentUrlRewritesFilter($storeId)) as $url) {
            $targetPath = null;
            if ($url->getRedirectType()) {
                $targetPath = str_replace(
                    $this->category->getOrigData('url_key'),
                    $this->category->getData('url_key'),
                    $url->getTargetPath()
                );
                $redirectType = $url->getRedirectType();
            } elseif ($this->category->getData('save_rewrites_history')) {
                $targetPath = str_replace(
                    $this->category->getOrigData('url_key'),
                    $this->category->getData('url_key'),
                    $url->getRequestPath()
                );
                $redirectType = OptionProvider::PERMANENT;
            }

            if ($targetPath && $url->getRequestPath() != $targetPath) {
                $urls[] = $this->createUrlRewrite($storeId, $url->getRequestPath(), $targetPath, $redirectType);
            }
        }
        return $urls;
    }

    /**
     * @param int $storeId
     * @return \Magento\UrlRedirect\Service\V1\Data\Filter
     */
    protected function createCurrentUrlRewritesFilter($storeId)
    {
        /** @var \Magento\UrlRedirect\Service\V1\Data\Filter $filter */
        $filter = $this->filterFactory->create();

        $filter->setStoreId($storeId);
        $filter->setEntityId($this->category->getId());
        $filter->setEntityType(self::ENTITY_TYPE_CATEGORY);
        return $filter;
    }

    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $targetPath
     * @param string|null $redirectType Null or one of OptionProvider const
     * @return UrlRewrite
     */
    protected function createUrlRewrite($storeId, $requestPath, $targetPath, $redirectType = null)
    {
        return $this->catalogUrlRewriteHelper->createUrlRewrite(
            self::ENTITY_TYPE_CATEGORY,
            $this->category->getId(),
            $storeId,
            $requestPath,
            $targetPath,
            $redirectType
        );
    }
}

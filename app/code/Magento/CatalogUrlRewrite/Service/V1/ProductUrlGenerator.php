<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRedirect\Model\Data\BuilderFactory;
use Magento\UrlRedirect\Model\Data\FilterFactory;
use Magento\UrlRedirect\Model\Data\UrlRewrite;
use Magento\UrlRedirect\Service\V1\UrlMatcherInterface;


/**
 * Product Generator
 * TODO: interface
 */
class ProductUrlGenerator
{
    // TODO:
    /** temporary solution for store product types */
    const TYPE = 'product';
    const TYPE_REDIRECT = 'product_redirect';
    // TODO:

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
     * @var BuilderFactory
     */
    protected $builderFactory;

    /**
     * @var CatalogUrlRewriteHelper
     */
    protected $catalogUrlRewriteHelper;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var null|\Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categories;

    /**
     * @param FilterFactory $filterFactory
     * @param StoreManagerInterface $storeManager
     * @param BuilderFactory $builderFactory
     * @param UrlMatcherInterface $urlMatcher
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     */
    public function __construct(
        FilterFactory $filterFactory,
        StoreManagerInterface $storeManager,
        BuilderFactory $builderFactory,
        UrlMatcherInterface $urlMatcher,
        CatalogUrlRewriteHelper $catalogUrlRewriteHelper
    ) {
        $this->filterFactory = $filterFactory;
        $this->storeManager = $storeManager;
        $this->builderFactory = $builderFactory;
        $this->urlMatcher = $urlMatcher;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;
        $this->product = null;
        $this->categories = null;
    }

    /**
     * Generate list of urls
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return UrlRewrite[]
     */
    public function generate(Product $product)
    {
        $this->product = $product;
        $storeId = $this->product->getStoreId();

        if ($this->catalogUrlRewriteHelper->isDefaultStore($storeId)) {
            $urls = $this->generateUrlForDefaultStore();
        } else {
            $urls = $this->generateForStore($storeId);
        }

        $this->product = null;
        $this->categories = null;
        return $urls;
    }

    /**
     * Generate list of urls for default store
     *
     * @return UrlRewrite[]
     */
    protected function generateUrlForDefaultStore()
    {
        $urls = [];
        foreach ($this->storeManager->getStores() as $store) {
            if ($this->catalogUrlRewriteHelper->isNeedCreateUrlRewrite($store->getStoreId(), $this->product->getId())) {
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
            $this->catalogUrlRewriteHelper->getProductUrlKeyPath($this->product, $storeId),
            $this->catalogUrlRewriteHelper->getProductCanonicalUrlPath($this->product),
            self::TYPE
        );

        foreach ($this->getCategories() as $category) {
            $urls[] = $this->createUrlRewrite(
                $storeId,
                $this->catalogUrlRewriteHelper->getProductUrlKeyPathWithCategory($this->product, $category, $storeId),
                $this->catalogUrlRewriteHelper->getProductCanonicalUrlPathWithCategory($this->product, $category),
                self::TYPE
            );
        }
        return array_merge($urls, $this->generatePermanentRedirectUrlsForStore($storeId));
    }

    /**
     * Build redirect urls
     *
     * @param int $storeId
     * @return array
     */
    protected function generatePermanentRedirectUrlsForStore($storeId)
    {
        // TODO: don't clear logic
        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($this->createFilter($storeId)) as $url) {
            if ($url->getEntityType() == self::TYPE) {
                $targetPath = str_replace(
                    $this->product->getOrigData('url_key'),
                    $this->product->getData('url_key'),
                    $url->getRequestPath()
                );
            } else {
                $targetPath = str_replace(
                    $this->product->getOrigData('url_key'),
                    $this->product->getData('url_key'),
                    $url->getTargetPath()
                );
            }
            if ($url->getRequestPath() == $targetPath) {
                continue;
            }
            $urls[] = $this->createUrlRewrite($storeId, $url->getRequestPath(), $targetPath, self::TYPE_REDIRECT, 'RP');
        }
        return $urls;
    }

    /**
     * @param int $storeId
     * @return \Magento\UrlRedirect\Model\Data\Filter
     */
    protected function createFilter($storeId)
    {
        /** @var \Magento\UrlRedirect\Model\Data\Filter $filter */
        $filter = $this->filterFactory->create();

        $filter->setStoreId($storeId);
        $filter->setEntityId($this->product->getId());

        $entityTypes = [ProductStorage::TYPE_REDIRECT];
        if ($this->product->getData('save_rewrites_history')) {
            $entityTypes[] = ProductStorage::TYPE;
        }
        $filter->setEntityType($entityTypes);

        return $filter;
    }

    /**
     * Get categories assigned to product
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function getCategories()
    {
        if (!$this->categories) {
            $this->categories = $this->product->getCategoryCollection();
            $this->categories->addAttributeToSelect('url_key');
            $this->categories->addAttributeToSelect('url_path');
        }
        return $this->categories;
    }

    /**
     * Create url rewrite object
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $targetPath
     * @param string $entityType
     * @param string $redirectType
     * @return \Magento\UrlRedirect\Model\Data\UrlRewrite
     */
    protected function createUrlRewrite($storeId, $requestPath, $targetPath, $entityType, $redirectType = '')
    {
        $data = [
            UrlRewrite::ENTITY_TYPE => $entityType,
            UrlRewrite::ENTITY_ID => $this->product->getId(),
            UrlRewrite::STORE_ID => $storeId,
            UrlRewrite::REQUEST_PATH => $requestPath,
            UrlRewrite::TARGET_PATH => $targetPath,
            UrlRewrite::REDIRECT_TYPE => $redirectType,
        ];
        return $this->builderFactory->create()->populateWithArray($data)->create();
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRedirect\Model\OptionProvider;
use Magento\UrlRedirect\Service\V1\Data\Converter;
use Magento\UrlRedirect\Service\V1\Data\FilterFactory;
use Magento\UrlRedirect\Service\V1\Data\UrlRewrite;
use Magento\UrlRedirect\Service\V1\UrlMatcherInterface;

/**
 * Product Generator
 */
class ProductUrlGenerator implements ProductUrlGeneratorInterface
{
    /**
     * TODO: think about better place for this const
     *
     * Entity type
     */
    const ENTITY_TYPE_PRODUCT = 'product';

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @var Converter
     */
    protected $converter;

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
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var null|\Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categories;

    /**
     * @param ProductFactory $productFactory
     * @param FilterFactory $filterFactory
     * @param Converter $converter
     * @param StoreManagerInterface $storeManager
     * @param UrlMatcherInterface $urlMatcher
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     */
    public function __construct(
        ProductFactory $productFactory,
        FilterFactory $filterFactory,
        Converter $converter,
        StoreManagerInterface $storeManager,
        UrlMatcherInterface $urlMatcher,
        CatalogUrlRewriteHelper $catalogUrlRewriteHelper
    ) {
        $this->productFactory = $productFactory;
        $this->filterFactory = $filterFactory;
        $this->converter = $converter;
        $this->storeManager = $storeManager;
        $this->urlMatcher = $urlMatcher;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;
    }

    /**
     * {@inheritdoc}
     * TODO: fix service parameter
     */
    public function generate($product)
    {
        $this->product = $product;
        $storeId = $this->product->getStoreId();

        $urls = $this->catalogUrlRewriteHelper->isDefaultStore($storeId)
            ? $this->generateForDefaultStore() : $this->generateForStore($storeId);

        $this->product = null;
        $this->categories = null;
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
            $this->catalogUrlRewriteHelper->getProductCanonicalUrlPath($this->product)
        );

        foreach ($this->getCategories() as $category) {
            $urls[] = $this->createUrlRewrite(
                $storeId,
                $this->catalogUrlRewriteHelper->getProductUrlKeyPathWithCategory($this->product, $category, $storeId),
                $this->catalogUrlRewriteHelper->getProductCanonicalUrlPathWithCategory($this->product, $category)
            );
        }
        return array_merge($urls, $this->generatePermanentRewritesBasedOnCurrentRewrites($storeId));
    }

    /**
     * Processing current rewrites
     *
     * @param int $storeId
     * @return array
     */
    protected function generatePermanentRewritesBasedOnCurrentRewrites($storeId)
    {
        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($this->createCurrentUrlRewritesFilter($storeId)) as $url) {
            $targetPath = null;
            if ($url->getRedirectType()) {
                $targetPath = str_replace(
                    $this->product->getOrigData('url_key'),
                    $this->product->getData('url_key'),
                    $url->getTargetPath()
                );
                $redirectType = $url->getRedirectType();
            } elseif ($this->product->getData('save_rewrites_history')) {
                $targetPath = str_replace(
                    $this->product->getOrigData('url_key'),
                    $this->product->getData('url_key'),
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
        $filter->setEntityId($this->product->getId());
        $filter->setEntityType(self::ENTITY_TYPE_PRODUCT);
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
     * @param string|null $redirectType Null or one of OptionProvider const
     * @return UrlRewrite
     */
    protected function createUrlRewrite($storeId, $requestPath, $targetPath, $redirectType = null)
    {
        return $this->converter->convertArrayToObject([
            UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE_PRODUCT,
            UrlRewrite::ENTITY_ID => $this->product->getId(),
            UrlRewrite::STORE_ID => $storeId,
            UrlRewrite::REQUEST_PATH => $requestPath,
            UrlRewrite::TARGET_PATH => $targetPath,
            UrlRewrite::REDIRECT_TYPE => $redirectType,
        ]);
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\Converter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;

/**
 * Product Generator
 *
 * TODO: abstract class
 */
class ProductUrlGenerator implements ProductUrlGeneratorInterface
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
     * @var CatalogUrlRewriteHelper
     */
    protected $catalogUrlRewriteHelper;

    /**
     * @var StoreViewService
     */
    protected $storeViewService;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var null|\Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categories;

    /**
     * @var \Magento\Catalog\Model\Category[]
     */
    protected $changedCategories;

    /**
     * @param FilterFactory $filterFactory
     * @param UrlMatcherInterface $urlMatcher
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     * @param StoreViewService $storeViewService
     * @param Converter $converter
     */
    public function __construct(
        FilterFactory $filterFactory,
        UrlMatcherInterface $urlMatcher,
        CatalogUrlRewriteHelper $catalogUrlRewriteHelper,
        StoreViewService $storeViewService,
        Converter $converter
    ) {
        $this->filterFactory = $filterFactory;
        $this->urlMatcher = $urlMatcher;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;
        $this->storeViewService = $storeViewService;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     * TODO: fix service parameter (@TODO: UrlRewrite)
     */
    public function generate($product)
    {
        $this->product = $product;
        $storeId = $this->product->getStoreId();

        $urls = $this->storeViewService->isGlobalScope($storeId)
            ? $this->generateForGlobalScope() : $this->generateForSpecificStoreView($storeId);

        $this->product = null;
        $this->categories = null;
        return $urls;
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
        return [$this->createUrlRewrite(
            $storeId,
            $this->catalogUrlRewriteHelper->getProductUrlKeyPath($this->product, $storeId),
            $this->catalogUrlRewriteHelper->getProductCanonicalUrlPath($this->product)
        )];
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
        // TODO: check if $category is not in this tore view (@TODO: UrlRewrite)
        foreach ($this->getCategories() as $category) {
            // TODO: hack for obtaining data from changed categories. Replace on Service Data Object (@TODO: UrlRewrite)
            if (isset($this->changedCategories[$category->getId()])) {
                $category = $this->changedCategories[$category->getId()];
            }
            if ($this->storeViewService->isRootCategoryForStore($category->getId(), $storeId)) {
                continue;
            }
            $urls[] = $this->createUrlRewrite(
                $storeId,
                $this->catalogUrlRewriteHelper->getProductUrlKeyPathWithCategory($this->product, $category, $storeId),
                $this->catalogUrlRewriteHelper->getProductCanonicalUrlPathWithCategory($this->product, $category)
            );
        }
        return $urls;
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

        $filter->setStoreId($storeId);
        $filter->setEntityId($this->product->getId());
        $filter->setEntityType(self::ENTITY_TYPE_PRODUCT);

        $urls = [];
        foreach ($this->urlMatcher->findAllByFilter($filter) as $url) {
            $targetPath = null;
            // TODO: 'Create Permanent Redirect for old URL' for products thorough category page (@TODO: UrlRewrite)
            if ($url->getRedirectType()) {
                // TODO: this is wrong logic (@TODO: UrlRewrite)
                $targetPath = str_replace(
                    $this->product->getOrigData('url_key'),
                    $this->product->getData('url_key'),
                    $url->getTargetPath()
                );
                $redirectType = $url->getRedirectType();
            } elseif ($this->product->getData('save_rewrites_history')) {
                // TODO: this is wrong logic (@TODO: UrlRewrite)
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
     * {@inheritdoc}
     *  TODO: hack for obtaining data from changed categories. Replace on Service Data Object (@TODO: UrlRewrite)
     */
    public function generateWithChangedCategories($product, $changedCategories)
    {
        $this->changedCategories = $changedCategories;

        return $this->generate($product);
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
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite($storeId, $requestPath, $targetPath, $redirectType = null)
    {
        return $this->converter->convertArrayToObject([
            UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE_PRODUCT,
            UrlRewrite::ENTITY_ID => $this->product->getId(),
            UrlRewrite::IS_AUTOGENERATED => 1,
            UrlRewrite::STORE_ID => $storeId,
            UrlRewrite::REQUEST_PATH => $requestPath,
            UrlRewrite::TARGET_PATH => $targetPath,
            UrlRewrite::REDIRECT_TYPE => $redirectType,
        ]);
    }
}

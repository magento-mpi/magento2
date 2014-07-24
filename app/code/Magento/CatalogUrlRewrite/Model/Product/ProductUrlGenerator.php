<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;
// TODO: UrlRewrite
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;

/**
 * Product Generator
 */
class ProductUrlGenerator
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
    protected $urlPathGenerator;

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

    /**
     * @param FilterFactory $filterFactory
     * @param UrlMatcherInterface $urlMatcher
     * @param CatalogUrlRewriteHelper $urlPathGenerator
     * @param StoreViewService $storeViewService
     * @param Converter $converter
     */
    public function __construct(
        FilterFactory $filterFactory,
        UrlMatcherInterface $urlMatcher,
        // TODO: MAGETWO-26285
        CatalogUrlRewriteHelper $urlPathGenerator,
        StoreViewService $storeViewService,
        Converter $converter
    ) {
        $this->filterFactory = $filterFactory;
        $this->urlMatcher = $urlMatcher;
        $this->urlPathGenerator = $urlPathGenerator;
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

        $urls = $this->storeViewService->isGlobalScope($storeId)
            ? $this->generateForGlobalScope() : $this->generateForSpecificStoreView($storeId);

        $this->product = null;
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
            $this->urlPathGenerator->getProductUrlKeyPath($this->product, $storeId),
            $this->urlPathGenerator->getProductCanonicalUrlPath($this->product)
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
        foreach ($this->product->getCategoryCollection() as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            if ($this->storeViewService->isRootCategoryForStore($category->getId(), $storeId)) {
                continue;
            }
            if (!in_array($storeId, $category->getStoreIds())) {
                continue;
            }
            $urls[] = $this->createUrlRewrite(
                $storeId,
                $this->urlPathGenerator->getProductUrlKeyPathWithCategory($this->product, $category, $storeId),
                $this->urlPathGenerator->getProductCanonicalUrlPathWithCategory($this->product, $category)
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
        // TODO: UrlRewrite - clone, update sql etc...
        // TODO: MAGETWO-26285
        foreach ($this->urlMatcher->findAllByFilter($filter) as $url) {
            if ($url->getIsAutogenerated()) {
                if ($this->product->getData('save_rewrites_history')) {
                    $urls[] = $this->createUrlRewrite(
                        $url->getStoreId(),
                        $url->getTargetPath(),// as request path
                        'new_product_url',
                        OptionProvider::PERMANENT
                    );
                }
            } else {
                if ($url->getRedirectType()) {
                    $urls[] = $this->createUrlRewrite(
                        $url->getStoreId(),
                        $url->getRequestPath(),
                        'new_product_url',
                        $url->getRedirectType()
                    );

                    if ($this->product->getData('save_rewrites_history')) {
                        $urls[] = $this->createUrlRewrite(
                            $url->getStoreId(),
                            $url->getTargetPath(),// as request path
                            'new_product_url',
                            $url->getRedirectType(),
                            true
                        );
                    }
                } else {
                    // it is custom internal redirect
                    $urls[] = $this->createUrlRewrite(
                        $url->getStoreId(),
                        $url->getRequestPath(),
                        $url->getTargetPath(),
                        $url->getRedirectType(),
                        $url->getIsAutoGenerated()
                    );
                }
            }
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
        return $this->converter->convertArrayToObject([
            UrlRewrite::ENTITY_TYPE => self::ENTITY_TYPE_PRODUCT,
            UrlRewrite::ENTITY_ID => $this->product->getId(),
            UrlRewrite::STORE_ID => $storeId,
            UrlRewrite::REQUEST_PATH => $requestPath,
            UrlRewrite::TARGET_PATH => $targetPath,
            UrlRewrite::REDIRECT_TYPE => $redirectType,
            UrlRewrite::IS_AUTOGENERATED => $isAutoGenerated,
        ]);
    }
}

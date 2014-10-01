<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Search;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogSearch\Model\QueryFactory;
use Magento\Framework\StoreManagerInterface;

class CollectionFilter implements CollectionFilterInterface
{
    /**
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @var \Magento\CatalogSearch\Model\QueryFactory
     */
    protected $queryFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @param Config $catalogConfig
     * @param \Magento\CatalogSearch\Model\QueryFactory $queryFactory
     * @param StoreManagerInterface $storeManager
     * @param Visibility $productVisibility
     */
    public function __construct(
        Config $catalogConfig,
        QueryFactory $queryFactory,
        StoreManagerInterface $storeManager,
        Visibility $productVisibility
    ) {
        $this->catalogConfig = $catalogConfig;
        $this->queryFactory = $queryFactory;
        $this->storeManager = $storeManager;
        $this->productVisibility = $productVisibility;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param \Magento\Catalog\Model\Category $category
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function filter(
        $collection,
        \Magento\Catalog\Model\Category $category
    ) {
        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addSearchFilter($this->queryFactory->getQuery()->getQueryText())
            ->setStore($this->storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInSearchIds());
    }
}

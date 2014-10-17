<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Search;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\DB\Select;
use Magento\Framework\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;

class CollectionFilter implements CollectionFilterInterface
{
    /**
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @var \Magento\Search\Model\QueryFactory
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
     * @param \Magento\Search\Model\QueryFactory $queryFactory
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
            ->addSearchFilter($this->queryFactory->get()->getQueryText())
            ->setStore($this->storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInSearchIds())
            ->setOrder('relevance', Select::SQL_ASC);
    }
}

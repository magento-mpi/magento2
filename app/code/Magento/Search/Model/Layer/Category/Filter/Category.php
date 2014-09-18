<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Category\Filter;

/**
 * Layer category filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Category extends \Magento\Catalog\Model\Layer\Filter\Category
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = array()
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $categoryFactory,
            $escaper,
            $coreRegistry,
            $data
        );
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        /** @var $category \Magento\Catalog\Model\Categeory */
        $category = $this->getCategory();
        $categories = $category->getChildrenCategories();

        $productCollection = $this->getLayer()->getProductCollection();
        $facets = $productCollection->getFacetedData('category_ids');

        $data = array();
        foreach ($categories as $category) {
            $categoryId = $category->getId();
            if (isset($facets[$categoryId])) {
                $category->setProductCount($facets[$categoryId]);
            } else {
                $category->setProductCount(0);
            }

            if ($category->getIsActive() && $category->getProductCount()) {
                $data[] = array(
                    'label' => $this->_escaper->escapeHtml($category->getName()),
                    'value' => $categoryId,
                    'count' => $category->getProductCount()
                );
            }
        }

        return $data;
    }

    /**
     * Add params to faceted search
     *
     * @return \Magento\Catalog\Model\Layer\Filter\Category
     */
    public function addFacetCondition()
    {
        $category = $this->getCategory();
        $childrenCategories = $category->getChildrenCategories();

        $useFlat = (bool)$this->_scopeConfig->getValue(
            'catalog/frontend/flat_catalog_category',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $categories = $useFlat ? array_keys($childrenCategories) : array_keys($childrenCategories->toArray());

        $this->getLayer()->getProductCollection()->setFacetCondition('category_ids', $categories);

        return $this;
    }
}

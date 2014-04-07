<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Category\Filter;

/**
 * Layer category filter
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Category extends \Magento\Catalog\Model\Layer\Filter\Category
{
    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Escaper $escaper
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Escaper $escaper,
        \Magento\Registry $coreRegistry,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        array $data = array()
    ) {
        $this->_storeConfig = $coreStoreConfig;
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

        $useFlat = (bool)$this->_storeConfig->getValue(
            'catalog/frontend/flat_catalog_category',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $categories = $useFlat ? array_keys($childrenCategories) : array_keys($childrenCategories->toArray());

        $this->getLayer()->getProductCollection()->setFacetCondition('category_ids', $categories);

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer category filter
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Search_Model_Catalog_Layer_Filter_Category extends Magento_Catalog_Model_Layer_Filter_Category
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($coreRegistry, $data);
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        /** @var $category Magento_Catalog_Model_Categeory */
        $category   = $this->getCategory();
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
                    'label' => Mage::helper('Magento_Core_Helper_Data')->escapeHtml($category->getName()),
                    'value' => $categoryId,
                    'count' => $category->getProductCount(),
                );
            }
        }

        return $data;
    }

    /**
     * Add params to faceted search
     *
     * @return Magento_Search_Model_Catalog_Layer_Filter_Category
     */
    public function addFacetCondition()
    {
        $category = $this->getCategory();
        $childrenCategories = $category->getChildrenCategories();

        $useFlat = (bool)$this->_coreStoreConfig->getConfig('catalog/frontend/flat_catalog_category');
        $categories = ($useFlat)
            ? array_keys($childrenCategories)
            : array_keys($childrenCategories->toArray());

        $this->getLayer()->getProductCollection()->setFacetCondition('category_ids', $categories);

        return $this;
    }
}

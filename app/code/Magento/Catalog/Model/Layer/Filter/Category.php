<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer category filter
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Layer_Filter_Category extends Magento_Catalog_Model_Layer_Filter_Abstract
{
    /**
     * Active Category Id
     *
     * @var int
     */
    protected $_categoryId;

    /**
     * Applied Category
     *
     * @var Magento_Catalog_Model_Category
     */
    protected $_appliedCategory = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($data);
        $this->_requestVar = 'cat';
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue()
    {
        if ($this->_appliedCategory) {
            /**
             * Revert path ids
             */
            $pathIds = array_reverse($this->_appliedCategory->getPathIds());
            $curCategoryId = $this->getLayer()->getCurrentCategory()->getId();
            if (isset($pathIds[1]) && $pathIds[1] != $curCategoryId) {
                return $pathIds[1];
            }
        }
        return null;
    }

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Magento_Core_Block_Abstract $filterBlock
     * @return  Magento_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = (int) $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }
        $this->_categoryId = $filter;

        Mage::register('current_category_filter', $this->getCategory(), true);

        $this->_appliedCategory = Mage::getModel('Magento_Catalog_Model_Category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($filter);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($this->_appliedCategory);

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_appliedCategory->getName(), $filter)
            );
        }

        return $this;
    }

    /**
     * Validate category for be using as filter
     *
     * @param   Magento_Catalog_Model_Category $category
     * @return unknown
     */
    protected function _isValidCategory($category)
    {
        return $category->getId();
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return __('Category');
    }

    /**
     * Get selected category object
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getCategory()
    {
        if (!is_null($this->_categoryId)) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')
                ->load($this->_categoryId);
            if ($category->getId()) {
                return $category;
            }
        }
        return $this->getLayer()->getCurrentCategory();
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $categoty   = $this->getCategory();
        /** @var $categoty Magento_Catalog_Model_Categeory */
        $categories = $categoty->getChildrenCategories();

        $this->getLayer()->getProductCollection()
            ->addCountToCategories($categories);

        $data = array();
        foreach ($categories as $category) {
            if ($category->getIsActive() && $category->getProductCount()) {
                $data[] = array(
                    'label' => $this->_coreData->escapeHtml($category->getName()),
                    'value' => $category->getId(),
                    'count' => $category->getProductCount(),
                );
            }
        }
        return $data;
    }
}

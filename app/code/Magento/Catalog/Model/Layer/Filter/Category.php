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
namespace Magento\Catalog\Model\Layer\Filter;

class Category extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
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
     * @var \Magento\Catalog\Model\Category
     */
    protected $_appliedCategory;

    /**
     * Core data
     *
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * Category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Escaper $escaper
     * @param \Magento\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Escaper $escaper,
        \Magento\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_escaper = $escaper;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($filterItemFactory, $storeManager, $catalogLayer, $data);
        $this->_requestVar = 'cat';
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed|null
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
     * @param   \Zend_Controller_Request_Abstract $request
     * @return  $this
     */
    public function apply(\Zend_Controller_Request_Abstract $request)
    {
        $filter = (int)$request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }
        $this->_categoryId = $filter;
        $this->_coreRegistry->register('current_category_filter', $this->getCategory(), true);

        $this->_appliedCategory = $this->_categoryFactory->create()
            ->setStoreId($this->_storeManager->getStore()->getId())
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
     * @param  \Magento\Catalog\Model\Category $category
     * @return mixed
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
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        if (!is_null($this->_categoryId)) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->_categoryFactory->create()
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
        $category   = $this->getCategory();
        $categories = $category->getChildrenCategories();

        $this->getLayer()->getProductCollection()
            ->addCountToCategories($categories);

        $data = array();
        foreach ($categories as $category) {
            if ($category->getIsActive() && $category->getProductCount()) {
                $data[] = array(
                    'label' => $this->_escaper->escapeHtml($category->getName()),
                    'value' => $category->getId(),
                    'count' => $category->getProductCount(),
                );
            }
        }
        return $data;
    }
}

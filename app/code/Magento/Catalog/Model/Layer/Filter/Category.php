<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Filter;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Layer category filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param CategoryRepository $categoryRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Registry $coreRegistry,
        CategoryRepository $categoryRepository,
        array $data = array()
    ) {
        $this->_escaper = $escaper;
        $this->_coreRegistry = $coreRegistry;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($filterItemFactory, $storeManager, $layer, $data);
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

        $storeId = $this->_storeManager->getStore()->getId();
        try {
            $this->_appliedCategory = $this->categoryRepository->get($filter, $storeId);
        } catch (NoSuchEntityException $e) {
            return $this;
        }

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()->addCategoryFilter($this->_appliedCategory);

            $this->getLayer()->getState()->addFilter($this->_createItem($this->_appliedCategory->getName(), $filter));
        }

        return $this;
    }

    /**
     * Validate category for be using as filter
     *
     * @param  \Magento\Catalog\Model\Category $category
     * @return bool
     */
    protected function _isValidCategory($category)
    {
        while ($category->getLevel() != 0) {
            if (!$category->getIsActive()) {
                return false;
            }
            $category = $category->getParentCategory();
        }
        return true;
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
            try {
                return $this->categoryRepository->get($this->_categoryId);
            } catch (NoSuchEntityException $e) {
                // TODO: MAGETWO-30203
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
        $category = $this->getCategory();
        $categories = $category->getChildrenCategories();

        $this->getLayer()->getProductCollection()->addCountToCategories($categories);

        $data = array();
        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                if ($category->getIsActive() && $category->getProductCount()) {
                    $data[] = array(
                        'label' => $this->_escaper->escapeHtml($category->getName()),
                        'value' => $category->getId(),
                        'count' => $category->getProductCount()
                    );
                }
            }
        }
        return $data;
    }
}

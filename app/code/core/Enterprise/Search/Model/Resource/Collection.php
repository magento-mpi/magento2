<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Enterprise search collection resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Search_Model_Resource_Collection
    extends Enterprise_Enterprise_Model_Catalog_Resource_Eav_Mysql4_Product_Collection
{

    /**
     * Store search query text
     *
     * @var string
     */
    protected $_searchQueryText = '';

    /**
     * Store search query params
     *
     * @var array
     */
    protected $_searchQueryParams = array();

    /**
     * Store search query filters
     *
     * @var array
     */
    protected $_searchQueryFilters = array();

    /**
     * Store found entities ids
     *
     * @var array
     */
    protected $_searchedEntityIds = array();

    /**
     * Store found suggestions
     *
     * @var array
     */
    protected $_searchedSuggestions = array();

    /**
     * Store engine instance
     *
     * @var object
     */
    protected $_engine = null;

    /**
     * Store sort orders
     *
     * @var array
     */
    protected $_sortBy = array();

    /**
     * Add search query filter
     * Set search query
     *
     * @param   string $query
     * @return  Enterprise_Search_Model_Resource_Collection
     */
    public function addSearchFilter($query)
    {
        $this->_searchQueryText = $query;
        return $this;
    }

    /**
     * Add search query filter
     * Set search query parameters
     *
     * @param   string|array $param
     * @param   string|array $value
     *
     * @return  Enterprise_Search_Model_Resource_Collection
     */
    public function addSearchParam($param, $value = null)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value)
                $this->addSearchParam($field, $value);
        }
        else {
            if (!empty($value)) {
                $this->_searchQueryParams[$param] = $value;
            }
        }

        return $this;
    }

    /**
     * Add search query filter (qf)
     *
     * @param   string|array $param
     * @param   string|array $value
     *
     * @return  Enterprise_Search_Model_Resource_Collection
     */
    public function addSearchQfFilter($param, $value = null)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value) {
                $this->addSearchQfFilter($field, $value);
            }
        }
        else {
            if (isset($value)) {
                if ( isset($this->_searchQueryFilters[$param]) && !is_array($this->_searchQueryFilters[$param]) ) {
                    $this->_searchQueryFilters[$param] = array($this->_searchQueryFilters[$param]);
                    $this->_searchQueryFilters[$param][]=$value;
                } else {
                    $this->_searchQueryFilters[$param] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Add price search query filter (qf)
     *
     * @param   string|array $param
     * @param   string|array $value
     *
     * @return  Enterprise_Search_Model_Resource_Collection
     */
    public function addPriceQfFilter($param)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value)
                $this->_searchQueryFilters[$field] = $value;
        }
        return $this;
    }

    /**
     * Add advanced search query filter
     * Set search query
     *
     * @param   string $query
     * @return  Enterprise_Search_Model_Resource_Collection
     */
    public function addAdvancedSearchFilter($query)
    {
        return $this->addSearchFilter($query);
    }

    /**
     * Add sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Enterprise_Search_Model_Resource_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        $this->_sortBy[] = array($attribute => $dir);
        return $this;
    }

    /**
     * Search documents by query
     * Set found ids and number of found results
     *
     * @return Enterprise_Search_Model_Resource_Collection
     */
    protected function _beforeLoad()
    {
        $ids = $params = array();
        if ($this->_engine) {
            $store                 = Mage::app()->getStore();
            $params['store_id']    = $store->getId();
            $params['locale_code'] = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);

            if ($this->_sortBy) {
                $params['sort_by'] = $this->_sortBy;
            }
            $page                  = ($this->_curPage  > 0) ? $this->_curPage  : 1;
            $rowCount              = ($this->_pageSize > 0) ? $this->_pageSize : 1;
            $params['offset']      = (int)$rowCount * ($page - 1);
            $params['limit']       = (int)$rowCount;

            $params['filters'] = $this->_searchQueryFilters;

            if (!empty($this->_searchQueryParams)) {
                $params['ignore_handler'] = true;
                $query = $this->_searchQueryParams;
            }
            else {
                $query = $this->_searchQueryText;
            }

            $ids = (array)$this->_engine->getIdsByQuery($query, $params);
        }
        $this->_searchedEntityIds = &$ids;
        $this->getSelect()->where('e.entity_id IN (?)', $this->_searchedEntityIds);
        /**
         * To prevent limitations to the collection, becouse of new data logic
         */
        $this->_pageSize = false;
        return parent::_beforeLoad();
    }

    /**
     * Sort collection items by sort order of found ids
     *
     * @return Enterprise_Search_Model_Resource_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $sortedItems = array();
        foreach ($this->_searchedEntityIds as $id) {
            if (isset($this->_items[$id])) {
                $sortedItems[$id] = $this->_items[$id];
            }
        }
        $this->_items = &$sortedItems;
        return $this;
    }

    /**
     * Retrieve found number of items
     *
     * @return int
     */
    public function getSize()
    {
        $params = array();
        if (is_null($this->_totalRecords)) {
            $store                 = Mage::app()->getStore();
            $params['store_id']    = $store->getId();
            $params['locale_code'] = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
            $params['limit']       = 1;

            if (!empty($this->_searchQueryParams)) {
                $params['ignore_handler'] = true;
                $query = $this->_searchQueryParams;
            }
            else {
                $query = $this->_searchQueryText;
            }
            $params['filters'] = $this->_searchQueryFilters;

            $this->_engine->getIdsByQuery($query, $params);
            $this->_totalRecords = $this->_engine->getLastNumFound();
        }
        return $this->_totalRecords;
    }

    /**
     * Retrieve found number of items
     *
     * @return int
     */
    public function getFacets($params)
    {

      //  if (is_null($this->_totalRecords)) {
            $store                 = Mage::app()->getStore();
            $params['store_id']    = $store->getId();
            $params['locale_code'] = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
            $params['limit']       = 1;

            if (!empty($this->_searchQueryParams)) {
                $params['ignore_handler'] = true;
                $query = $this->_searchQueryParams;
            }
            else {
                $query = $this->_searchQueryText;
            }

            $params['filters'] = $this->_searchQueryFilters;

            //$this->_engine->getIdsByQuery($query, $params);
            $facets = (array)$this->_engine->getFacetsByQuery($query, $params);

          //  $this->_totalRecords = $this->_engine->getLastNumFound();
      //  }
        return $facets;
    }

    /**
     * Set search engine
     *
     * @param object $engine
     * @return Enterprise_Search_Model_Resource_Collection
     */
    public function setEngine($engine)
    {
        $this->_engine = $engine;
        return $this;
    }


    public function addFieldsToFilter($fields)
    {
        return $this;
    }


    /**
     * Adding product count to categories collection
     *
     * @param   Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addCountToCategories($categoryCollection)
    {

        $isAnchor = array();
        $isNotAnchor = array();
        foreach ($categoryCollection as $category) {
            if ($category->getIsAnchor()) {
                $isAnchor[] = $category->getId();
            } else {
                $isNotAnchor[] = $category->getId();
            }
        }
        $productCounts = array();
        if ($isAnchor || $isNotAnchor) {
            $select = $this->getProductCountSelect();

            Mage::dispatchEvent('catalog_product_collection_before_add_count_to_categories', array('collection'=>$this));
            if ($isAnchor) {
                //$anchorStmt = clone $select;
                //$anchorStmt->limit(); //reset limits
                //$anchorStmt->where('count_table.category_id in (?)', $isAnchor);
                //$productCounts += $this->getConnection()->fetchPairs($anchorStmt);

                $params = array();
                $params['facet']['field']  = 'categories';
                $params['facet']['values'] = $isAnchor;
                $res = $this->getFacets($params);

                $productCounts += $res['categories'];
                $anchorStmt = null;
            }
            if ($isNotAnchor) {
                //$notAnchorStmt = clone $select;
                //$notAnchorStmt->limit(); //reset limits
                //$notAnchorStmt->where('count_table.category_id in (?)', $isNotAnchor);
                //$notAnchorStmt->where('count_table.is_parent=1');
                //$productCounts += $this->getConnection()->fetchPairs($notAnchorStmt);

                $params = array();
                $params['facet']['field']  = 'categories';
                $params['facet']['values'] = $isNotAnchor;
                $res = $this->getFacets($params);
                $productCounts += $res['categories'];

                $notAnchorStmt = null;
            }
            $select = null;
            $this->unsProductCountSelect();
        }

        foreach ($categoryCollection as $category) {
            $_count = 0;
            if (isset($productCounts[$category->getId()])) {
                $_count = $productCounts[$category->getId()];
            }
            $category->setProductCount($_count);
        }
        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param array $visibility
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function setVisibility($visibility)
    {
        if (is_array($visibility)) {
            foreach ($visibility as $visibilityId) {
                $this->addSearchQfFilter('visibility', $visibilityId);
            }
        }
        return $this;
    }

    /**
     * Specify category filter for product collection
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
        $this->addSearchQfFilter('categories', $category->getId());
/*
        $this->_productLimitationFilters['category_id'] = $category->getId();
        if ($category->getIsAnchor()) {
            unset($this->_productLimitationFilters['category_is_anchor']);
        }
        else {
            $this->_productLimitationFilters['category_is_anchor'] = 1;
        }

        ($this->getStoreId() == 0)? $this->_applyZeroStoreProductLimitations() : $this->_applyProductLimitations();
*/
        return $this;
    }
}

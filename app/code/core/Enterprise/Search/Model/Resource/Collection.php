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
class Enterprise_Search_Model_Resource_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    /**
     * Store search query text
     *
     * @var string
     */
    protected $_searchQueryText = '';

    /**
     * Store found entities ids
     *
     * @var array
     */
    protected $_searchedEntityIds = array();

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
            $page     = ($this->_curPage  > 0) ? $this->_curPage  : 1;
            $rowCount = ($this->_pageSize > 0) ? $this->_pageSize : 1;
            $params['offset'] = (int)$rowCount * ($page - 1);
            $params['limit']  = (int)$rowCount;
            $ids = (array)$this->_engine->getIdsByQuery($this->_searchQueryText, $params);
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

            $this->_engine->getIdsByQuery($this->_searchQueryText, $params);
            $this->_totalRecords = $this->_engine->getLastNumFound();
        }
        return $this->_totalRecords;
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
}

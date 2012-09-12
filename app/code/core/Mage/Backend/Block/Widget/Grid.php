<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid extends Mage_Backend_Block_Widget
{
    /**
     * Collection object
     *
     * @var Varien_Data_Collection
     */
    protected $_collection = null;

    /**
     * Page and sorting var names
     *
     * @var string
     */
    protected $_varNameLimit    = 'limit';
    protected $_varNamePage     = 'page';
    protected $_varNameSort     = 'sort';
    protected $_varNameDir      = 'dir';
    protected $_varNameFilter   = 'filter';

    protected $_defaultLimit    = 20;
    protected $_defaultPage     = 1;
    protected $_defaultSort     = false;
    protected $_defaultDir      = 'desc';
    protected $_defaultFilter   = array();

    /**
     * Empty grid text
     *
     * @var sting|null
     */
    protected $_emptyText;

    /**
     * Empty grid text CSS class
     *
     * @var sting|null
     */
    protected $_emptyTextCss    = 'a-center';

    /**
     * Pager visibility
     *
     * @var boolean
     */
    protected $_pagerVisibility = true;

    /**
     * Column headers visibility
     *
     * @var boolean
     */
    protected $_headersVisibility = true;

    /**
     * Filter visibility
     *
     * @var boolean
     */
    protected $_filterVisibility = true;

    /**
     * Massage block visibility
     *
     * @var boolean
     */
    protected $_messageBlockVisibility = false;

    /**
     * Should parameters be saved in session
     *
     * @var bool
     */
    protected $_saveParametersInSession = false;

    /**
     * Count totals
     *
     * @var boolean
     */
    protected $_countTotals = false;

    /**
     * Count subtotals
     *
     * @var boolean
     */
    protected $_countSubTotals = false;

    /**
     * Totals
     *
     * @var Varien_Object
     */
    protected $_varTotals;

    /**
     * SubTotals
     *
     * @var array
     */
    protected $_subtotals = array();

    /**
     * RSS list
     *
     * @var array
     */
    protected $_rssLists = array();

    /**
     * Columns to group by
     *
     * @var array
     */
    protected $_groupedColumn = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->setTemplate('Mage_Backend::widget/grid.phtml');
        $this->setRowClickCallback('openGridRow');
        $this->_emptyText = Mage::helper('Mage_Backend_Helper_Data')->__('No records found.');
    }

    /**
     * Set collection object
     *
     * @param Varien_Data_Collection $collection
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
    }

    /**
     * get collection object
     *
     * @return Varien_Data_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Retrieve column set block
     *
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function getColumnSet()
    {
        return $this->getChildBlock('grid.columnSet');
    }

    /**
     * Retrieve list of grid columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->getColumnSet()->getColumns();
    }

    /**
     * Count grid columns
     *
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
    }

    /**
     * Retrieve column by id
     *
     * @param string $columnId
     * @return Mage_Core_Block_Abstract
     */
    public function getColumn($columnId)
    {
        return $this->getColumnSet()->getChildBlock($columnId);
    }

    /**
     * Process column filtration values
     *
     * @param mixed $data
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($data[$columnId])
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()
            ) {
                $column->getFilter()->setValue($data[$columnId]);
                $this->_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }

    /**
     * Add column filtering conditions to collection
     *
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                    $this->getCollection()->addFieldToFilter($field, $cond);
                }
            }
        }
        return $this;
    }

    /**
     * Sets sorting order by some column
     *
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }
        return $this;
    }

    public function getPreparedCollection()
    {
        $this->_prepareCollection();
        return $this->getCollection();
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('Mage_Backend_Helper_Data')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if ($this->getColumn($columnId) && $this->getColumn($columnId)->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->getColumn($columnId)->setDir($dir);
                $this->_setCollectionOrder($this->getColumn($columnId));
            }
        }

        return $this;
    }

    /**
     * Decode URL encoded filter value recursive callback method
     *
     * @var string $value
     */
    protected function _decodeFilter(&$value)
    {
        $value = $this->helper('Mage_Backend_Helper_Data')->decodeFilter($value);
    }

    /**
     * Apply pagination to collection
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize((int) $this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        $this->getCollection()->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    /**
     * Initialize grid
     */
    protected function _prepareGrid()
    {
        $this->_prepareCollection();
        if ($this->hasColumnRenderers()) {
            foreach ($this->getColumnRenderers() as $renderer => $rendererClass) {
                $this->getColumnSet()->setRendererType($renderer, $rendererClass);
            }
        }
        if ($this->hasColumnFilters()) {
            foreach ($this->getColumnFilters() as $filter => $filterClass) {
                $this->getColumnSet()->setFilterType($filter, $filterClass);
            }
        }
        $this->getColumnSet()->setSortable($this->getSortable());
    }

    /**
     * Initialize grid before rendering
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->_prepareGrid();
        $this->getColumnSet()->initColumns();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve limit request key
     *
     * @return string
     */
    public function getVarNameLimit()
    {
        return $this->_varNameLimit;
    }

    /**
     * Retrieve page request key
     *
     * @return string
     */
    public function getVarNamePage()
    {
        return $this->_varNamePage;
    }

    /**
     * Retrieve sort request key
     *
     * @return string
     */
    public function getVarNameSort()
    {
        return $this->_varNameSort;
    }

    /**
     * Retrieve sort direction request key
     *
     * @return string
     */
    public function getVarNameDir()
    {
        return $this->_varNameDir;
    }

    /**
     * Retrieve filter request key
     *
     * @return string
     */
    public function getVarNameFilter()
    {
        return $this->_varNameFilter;
    }

    /**
     * Set Limit request key
     *
     * @param string $name
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setVarNameLimit($name)
    {
        $this->_varNameLimit = $name;
        return $this;
    }

    /**
     * Set Page request key
     *
     * @param string $name
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setVarNamePage($name)
    {
        $this->_varNamePage = $name;
        return $this;
    }

    /**
     * Set Sort request key
     *
     * @param string $name
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setVarNameSort($name)
    {
        $this->_varNameSort = $name;
        return $this;
    }

    /**
     * Set Sort Direction request key
     *
     * @param string $name
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setVarNameDir($name)
    {
        $this->_varNameDir = $name;
        return $this;
    }

    /**
     * Set Filter request key
     *
     * @param string $name
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setVarNameFilter($name)
    {
        $this->_varNameFilter = $name;
        return $this;
    }

    /**
     * Set visibility of column headers
     *
     * @param boolean $visible
     */
    public function setHeadersVisibility($visible=true)
    {
        $this->_headersVisibility = $visible;
    }

    /**
     * Return visibility of column headers
     *
     * @return boolean
     */
    public function getHeadersVisibility()
    {
        return $this->_headersVisibility;
    }

    /**
     * Set visibility of pager
     *
     * @param boolean $visible
     */
    public function setPagerVisibility($visible=true)
    {
        $this->_pagerVisibility = $visible;
    }

    /**
     * Return visibility of pager
     *
     * @return boolean
     */
    public function getPagerVisibility()
    {
        return $this->_pagerVisibility;
    }

    /**
     * Set visibility of filter
     *
     * @param boolean $visible
     */
    public function setFilterVisibility($visible=true)
    {
        $this->_filterVisibility = $visible;
    }

    /**
     * Return visibility of filter
     *
     * @return boolean
     */
    public function getFilterVisibility()
    {
        return $this->_filterVisibility;
    }

    /**
     * Set visibility of filter
     *
     * @param boolean $visible
     */
    public function setMessageBlockVisibility($visible=true)
    {
        $this->_messageBlockVisibility = $visible;
    }

    /**
     * Return visibility of filter
     *
     * @return boolean
     */
    public function getMessageBlockVisibility()
    {
        return $this->_messageBlockVisibility;
    }

    /**
     * Set default limit
     *
     * @param int $limit
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setDefaultLimit($limit)
    {
        $this->_defaultLimit = $limit;
        return $this;
    }

    /**
     * Set default page
     *
     * @param int $page
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setDefaultPage($page)
    {
        $this->_defaultPage = $page;
        return $this;
    }

    /**
     * Set default sort
     *
     * @param string $sort
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setDefaultSort($sort)
    {
        $this->_defaultSort = $sort;
        return $this;
    }

    /**
     * Set default direction
     *
     * @param string $dir
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setDefaultDir($dir)
    {
        $this->_defaultDir = $dir;
        return $this;
    }

    /**
     * Set default filter
     *
     * @param string $filter
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setDefaultFilter($filter)
    {
        $this->_defaultFilter = $filter;
        return $this;
    }

    /**
     * Retrieve rss lists types
     *
     * @return array|bool
     */
    public function getRssLists()
    {
        return empty($this->_rssLists) ? false : $this->_rssLists;
    }

    /**
     * Returns url for RSS
     * Can be overloaded in descendant classes to perform custom changes to url passed to addRssList()
     *
     * @param string $url
     * @return string
     */
    protected function _getRssUrl($url)
    {
        $urlModel = Mage::getModel('Mage_Core_Model_Url');
        if (Mage::app()->getStore()->getStoreInUrl()) {
            // Url in 'admin' store view won't be accessible, so form it in default store view frontend
            $urlModel->setStore(Mage::app()->getDefaultStoreView());
        }
        return $urlModel->getUrl($url);
    }

    /**
     * Add new rss list to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  Mage_Backend_Block_Widget_Grid
     */
    public function addRssList($url, $label)
    {
        $this->_rssLists[] = new Varien_Object(
            array(
                'url'   => $this->_getRssUrl($url),
                'label' => $label
            )
        );
        return $this;
    }

    /**
     * Clear rss list in grid
     *
     * @return  Mage_Backend_Block_Widget_Grid
     */
    public function clearRss()
    {
        $this->_rssLists = array();
        return $this;
    }

    /**
     * Check whether grid container should be displayed
     *
     * @return bool
     */
    public function canDisplayContainer()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            return false;
        }
        return true;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getCurrentUrl();
    }

    /**
     * Grid url getter
     * Version of getGridUrl() but with parameters
     *
     * @param array $params url parameters
     * @return string current grid url
     */
    public function getAbsoluteGridUrl($params = array())
    {
        return $this->getCurrentUrl($params);
    }

    /**
     * Retrieve grid
     *
     * @param   string $paramName
     * @param   mixed $default
     * @return  mixed
     */
    public function getParam($paramName, $default=null)
    {
        $session = Mage::getSingleton('Mage_Backend_Model_Session');
        $sessionParamName = $this->getId().$paramName;
        if ($this->getRequest()->has($paramName)) {
            $param = $this->getRequest()->getParam($paramName);
            if ($this->_saveParametersInSession) {
                $session->setData($sessionParamName, $param);
            }
            return $param;
        }
        elseif ($this->_saveParametersInSession && ($param = $session->getData($sessionParamName)))
        {
            return $param;
        }

        return $default;
    }

    /**
     * Set whether grid parameters should be saved in session
     *
     * @param bool $flag
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setSaveParametersInSession($flag)
    {
        $this->_saveParametersInSession = $flag;
        return $this;
    }

    /**
     * Retrieve grid javascript object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getId().'JsObject';
    }


    /**
     * Set empty text for grid
     *
     * @param string $text
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setEmptyText($text)
    {
        $this->_emptyText = $text;
        return $this;
    }

    /**
     * Return empty text for grid
     *
     * @return string
     */
    public function getEmptyText()
    {
        return $this->_emptyText;
    }

    /**
     * Set empty text CSS class
     *
     * @param string $cssClass
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setEmptyTextClass($cssClass)
    {
        $this->_emptyTextCss = $cssClass;
        return $this;
    }

    /**
     * Return empty text CSS class
     *
     * @return string
     */
    public function getEmptyTextClass()
    {
        return $this->_emptyTextCss;
    }

    /**
     * Set count totals
     *
     * @param boolean $visible
     */
    public function setCountTotals($count=true)
    {
        $this->_countTotals = $count;
    }

    /**
     * Return count totals
     *
     * @return bool
     */
    public function getCountTotals()
    {
        return $this->_countTotals;
    }

    /**
     * Set totals
     *
     * @param Varien_Object $totals
     */
    public function setTotals(Varien_Object $totals)
    {
        $this->_varTotals = $totals;
    }

    /**
     * Retrieve totals
     *
     * @return Varien_Object
     */
    public function getTotals()
    {
        return $this->_varTotals;
    }

    /**
     * Set subtotals
     *
     * @param boolean $flag
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setCountSubTotals($flag = true)
    {
        $this->_countSubTotals = $flag;
        return $this;
    }

    /**
     * Return count subtotals
     *
     * @return boolean
     */
    public function getCountSubTotals()
    {
        return $this->_countSubTotals;
    }

    /**
     * Set subtotal items
     *
     * @param array $items
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setSubTotals(array $items)
    {
        $this->_subtotals = $items;
        return $this;
    }

    /**
     * Retrieve subtotal item
     *
     * @param Varien_Object $item
     * @return Varien_Object
     */
    public function getSubTotalItem($item)
    {
        foreach ($this->_subtotals as $subtotalItem) {
            foreach ($this->_groupedColumn as $groupedColumn) {
                if ($subtotalItem->getData($groupedColumn) == $item->getData($groupedColumn)) {
                    return $subtotalItem;
                }
            }
        }
        return '';
    }

    /**
     * Retrieve subtotal items
     *
     * @return array
     */
    public function getSubTotals()
    {
        return $this->_subtotals;
    }

    /**
     * Check whether subtotal should be rendered
     *
     * @param Varien_Object $item
     * @return boolean
     */
    public function shouldRenderSubTotal($item) {
        return ($this->_countSubTotals && count($this->_subtotals) > 0 && count($this->getMultipleRows($item)) > 0);
    }


    /**
     * Retrieve rowspan number
     *
     * @param Varien_Object $item
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return integer|boolean
     */
    public function getRowspan($item, $column)
    {
        if ($this->isColumnGrouped($column)) {
            return count($this->getMultipleRows($item)) + count($this->_groupedColumn);
        }
        return false;
    }

    /**
     * Check whether given column is grouped
     *
     * @param string|object $column
     * @param string $value
     * @return boolean|Mage_Backend_Block_Widget_Grid
     */
    public function isColumnGrouped($column, $value = null)
    {
        if (null === $value) {
            if (is_object($column)) {
                return in_array($column->getIndex(), $this->_groupedColumn);
            }
            return in_array($column, $this->_groupedColumn);
        }
        $this->_groupedColumn[] = $column;
        return $this;
    }

    /**
     * Get children of specified item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return $item->getChildren();
    }

    /**
     * Retrieve columns for multiple rows
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRowColumns()
    {
        $columns = $this->getColumns();
        foreach ($this->_groupedColumn as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }


    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($item)
    {
        $res = parent::getRowUrl($item);
        return ($res ? $res : '#');
    }

    /**
     * Check whether should render empty cell
     *
     * @param Varien_Object $item
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return boolean
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        return ($item->getIsEmpty() && in_array($column['index'], $this->_groupedColumn));
    }

    /**
     * Retrieve colspan for empty cell
     *
     * @return int
     */
    public function getEmptyCellColspan()
    {
        return $this->getColumnCount() - count($this->_groupedColumn);
    }
}

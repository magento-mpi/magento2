<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend grid widget block
 *
 * @method string getRowClickCallback() getRowClickCallback()
 * @method \Magento\Backend\Block\Widget\Grid setRowClickCallback() setRowClickCallback(string $value)
 */
namespace Magento\Backend\Block\Widget;

class Grid extends \Magento\Backend\Block\Widget
{
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
     * @var string|null
     */
    protected $_emptyText;

    /**
     * Empty grid text CSS class
     *
     * @var string|null
     */
    protected $_emptyTextCss    = 'a-center';

    /**
     * Pager visibility
     *
     * @var boolean
     */
    protected $_pagerVisibility = true;

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
     * Totals
     *
     * @var \Magento\Object
     */
    protected $_varTotals;

    /**
     * RSS list
     *
     * @var array
     */
    protected $_rssLists = array();

    protected $_template = 'Magento_Backend::widget/grid.phtml';

    /**
     * @var \Magento\Core\Model\Url
     */
    protected $_urlModel;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = array()
    ) {
        $this->_backendHelper = $backendHelper;
        $this->_urlModel = $urlModel;
        $this->_backendSession = $context->getBackendSession();
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        if (!$this->getRowClickCallback()) {
            $this->setRowClickCallback('openGridRow');
        }

        if ($this->hasData('id')) {
            $this->setId($this->getData('id'));
        }

        if ($this->hasData('default_sort')) {
            $this->setDefaultSort($this->getData('default_sort'));
        }

        if ($this->hasData('default_dir')) {
            $this->setDefaultDir($this->getData('default_dir'));
        }

        if ($this->hasData('save_parameters_in_session')) {
            $this->setSaveParametersInSession($this->getData('save_parameters_in_session'));
        }

        $this->setPagerVisibility($this->hasData('pager_visibility')? (bool) $this->getData('pager_visibility') : true);

        $this->setData(
            'use_ajax',
            $this->hasData('use_ajax') ? (bool) $this->getData('use_ajax') : false
        );

        if ($this->hasData('rssList') && is_array($this->getData('rssList'))) {
            foreach ($this->getData('rssList') as $item) {
                $this->addRssList($item['url'], $item['label']);
            }
        }
    }

    /**
     * Set collection object
     *
     * @param \Magento\Data\Collection $collection
     */
    public function setCollection($collection)
    {
        $this->setData('dataSource', $collection);
    }

    /**
     * Get collection object
     *
     * @return \Magento\Data\Collection
     */
    public function getCollection()
    {
        return $this->getData('dataSource');
    }

    /**
     * Retrieve column set block
     *
     * @return \Magento\Backend\Block\Widget\Grid\ColumnSet
     */
    public function getColumnSet()
    {
        return $this->getChildBlock('grid.columnSet');
    }

    /**
     * Retrieve export block
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\View\Element\AbstractBlock
     */
    public function getExportBlock()
    {
        if (!$this->getChildBlock('grid.export')) {
            throw new \Magento\Core\Exception('Export block for grid ' . $this->getNameInLayout() . ' is not defined');
        }
        return $this->getChildBlock('grid.export');
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
     * @return \Magento\View\Element\AbstractBlock
     */
    public function getColumn($columnId)
    {
        return $this->getColumnSet()->getChildBlock($columnId);
    }

    /**
     * Process column filtration values
     *
     * @param mixed $data
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($data[$columnId])
                && ((is_array($data[$columnId]) && !empty($data[$columnId])) || strlen($data[$columnId]) > 0)
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
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $condition = $column->getFilter()->getCondition();
                if ($field && isset($condition)) {
                    $this->getCollection()->addFieldToFilter($field, $condition);
                }
            }
        }
        return $this;
    }

    /**
     * Sets sorting order by some column
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return \Magento\Backend\Block\Widget\Grid
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

    /**
     * Get prepared collection
     *
     * @return \Magento\Data\Collection
     */
    public function getPreparedCollection()
    {
        $this->_prepareCollection();
        return $this->getCollection();
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
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
                $data = $this->_backendHelper->prepareFilterString($filter);
                $data = array_merge($data, (array)$this->getRequest()->getPost($this->getVarNameFilter()));
                $this->_setFilterValues($data);
            } else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            } else if (0 !== sizeof($this->_defaultFilter)) {
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
        $value = $this->_backendHelper->decodeFilter($value);
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
        if ($this->getChildBlock('grid.massaction') && $this->getChildBlock('grid.massaction')->isAvailable()) {
            $this->getChildBlock('grid.massaction')->prepareMassactionColumn();
        }

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
        $this->_prepareFilterButtons();
    }

    /**
     * Get massaction block
     *
     * @return bool|\Magento\View\Element\AbstractBlock
     */
    public function getMassactionBlock()
    {
        return $this->getChildBlock('grid.massaction');
    }

    /**
     * Prepare grid filter buttons
     */
    protected function _prepareFilterButtons()
    {
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                'label'     => __('Reset Filter'),
                'onclick'   => $this->getJsObjectName().'.resetFilter()',
            ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                'label'     => __('Search'),
                'onclick'   => $this->getJsObjectName().'.doFilter()',
                'class'   => 'task'
            ))
        );
    }

    /**
     * Initialize grid before rendering
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $this->_prepareGrid();
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function setVarNameFilter($name)
    {
        $this->_varNameFilter = $name;
        return $this;
    }

    /**
     * Set visibility of pager
     *
     * @param boolean $visible
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function setPagerVisibility($visible = true)
    {
        $this->_pagerVisibility = $visible;
        return $this;
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
     * Set visibility of message blocks
     *
     * @param boolean $visible
     */
    public function setMessageBlockVisibility($visible = true)
    {
        $this->_messageBlockVisibility = $visible;
    }

    /**
     * Return visibility of message blocks
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * @return \Magento\Backend\Block\Widget\Grid
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
     * Add new rss list to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  \Magento\Backend\Block\Widget\Grid
     */
    public function addRssList($url, $label)
    {
        $this->_rssLists[] = new \Magento\Object(
            array(
                'url'   => $this->getUrl($url, array('_nosecret' => true)),
                'label' => $label
            )
        );
        return $this;
    }

    /**
     * Clear rss list in grid
     *
     * @return  \Magento\Backend\Block\Widget\Grid
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
     * Retrieve grid reload url
     *
     * @return string;
     */
    public function getGridUrl()
    {
        return $this->hasData('grid_url') ? $this->getData('grid_url') : $this->getAbsoluteGridUrl();
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
     * @param string $paramName
     * @param mixed $default
     * @return mixed
     */
    public function getParam($paramName, $default = null)
    {
        $sessionParamName = $this->getId() . $paramName;
        if ($this->getRequest()->has($paramName)) {
            $param = $this->getRequest()->getParam($paramName);
            if ($this->_saveParametersInSession) {
                $this->_backendSession->setData($sessionParamName, $param);
            }
            return $param;
        } elseif ($this->_saveParametersInSession && ($param = $this->_backendSession->getData($sessionParamName))) {
            return $param;
        }

        return $default;
    }

    /**
     * Set whether grid parameters should be saved in session
     *
     * @param bool $flag
     * @return \Magento\Backend\Block\Widget\Grid
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
        return $this->getId() . 'JsObject';
    }

    /**
     * Set count totals
     *
     * @param boolean $count
     * @return \Magento\Backend\Block\Widget\Grid
     */
    public function setCountTotals($count = true)
    {
        $this->_countTotals = $count;
        return $this;
    }

    /**
     * Return count totals
     *
     * @return boolean
     */
    public function getCountTotals()
    {
        return $this->_countTotals;
    }

    /**
     * Set totals
     *
     * @param \Magento\Object $totals
     */
    public function setTotals(\Magento\Object $totals)
    {
        $this->_varTotals = $totals;
    }

    /**
     * Retrieve totals
     *
     * @return \Magento\Object
     */
    public function getTotals()
    {
        return $this->_varTotals;
    }

    /**
     * Generate list of grid buttons
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ($this->getColumnSet()->isFilterVisible()) {
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        return $html;
    }

    /**
     * Generate reset button
     *
     * @return string
     */
    public function getResetFilterButtonHtml()
    {
        return $this->getChildHtml('reset_filter_button');
    }

    /**
     * Generate search button
     *
     * @return string
     */
    public function getSearchButtonHtml()
    {
        return $this->getChildHtml('search_button');
    }
}

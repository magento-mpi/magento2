<?php
/**
 * Adminhtml grid widget block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid extends Mage_Adminhtml_Block_Widget
{
    /**
     * Columns array
     *
     * array(
     *      'header'    => string,
     *      'width'     => int,
     *      'sortable'  => bool,
     *      'index'     => string,
     *      //'renderer'  => Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface,
     *      'format'    => string
     * )
     * @var array
     */
    protected $_columns = array();

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
     * Pager visibility
     *
     * @var boolean
     */
    protected $_pagerVisibility = true;

    /**
     * Filter visibility
     *
     * @var boolean
     */
    protected $_filterVisibility = true;

    protected $_saveParametersInSession = false;

    /**
     * Grid export types
     *
     * @var array
     */
    protected $_exportTypes = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/widget/grid.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('exportButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Export'),
                    'onclick'   => $this->getJsObjectName().'.doExport()'
                ))
        );
        $this->setChild('resetFilterButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()'
                ))
        );
        $this->setChild('searchButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()'
                ))
        );
    }

    public function getExportButtonHtml()
    {
        return $this->getChildHtml('exportButton');
    }

    public function getResetFilterButtonHtml()
    {
        return $this->getChildHtml('resetFilterButton');
    }

    public function getSearchButtonHtml()
    {
        return $this->getChildHtml('searchButton');
    }

    /**
     * set collection object
     *
     * @param Varien_Data_Collection $collection
     */
    //public function setCollection(Varien_Data_Collection $collection)
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
     * Add column to grid
     *
     * @param   string $columnId
     * @param   array || Varien_Object $column
     * @return  Mage_Adminhtml_Block_Widget_Grid
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $this->_columns[$columnId] = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
                ->setData($column)
                ->setGrid($this);
        }
        /*elseif ($column instanceof Varien_Object) {
            $this->_columns[$columnId] = $column;
        }*/
        else {
            throw new Exception('Wrong column format');
        }

        $this->_columns[$columnId]->setId($columnId);
        return $this;
    }

    /**
     * Retrieve grid column by column id
     *
     * @param   string $columnId
     * @return  Varien_Object || false
     */
    public function getColumn($columnId)
    {
        if (!empty($this->_columns[$columnId])) {
            return $this->_columns[$columnId];
        }
        return false;
    }

    /**
     * Retrieve all grid columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    protected function _setFilterValues($data)
    {
        foreach ($this->getColumns() as $columnId => $column) {
            if (!empty($data[$columnId]) && $column->getFilter()) {
                $column->getFilter()->setValue($data[$columnId]);
                $this->_addColumnFilterToCollection($column);
            }
        }
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $this->getCollection()->addFieldToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        return $this;
    }

    /**
     * Prepare grid collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), $this->_defaultFilter);

            if ($filter && is_string($filter)) {
                $data = array();
                $filter = base64_decode($filter);
                parse_str(urldecode($filter), $data);
                $this->_setFilterValues($data);
            } else if ($filter && is_array($filter)) {
            	$this->_setFilterValues($filter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->getCollection()->setOrder($this->_columns[$columnId]->getIndex(), $dir);
            }

            $this->getCollection()->load();
        }

        return $this;
    }

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        $this->getCollection()->setCurPage($this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    protected function _prepareColumns()
    {
        return $this;
    }

    protected function _prepareGrid()
    {
        $this->_prepareColumns();
        $this->_prepareCollection();
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareGrid();
        return $this;
    }

    public function getVarNameLimit()
    {
        return $this->_varNameLimit;
    }

    public function getVarNamePage()
    {
        return $this->_varNamePage;
    }

    public function getVarNameSort()
    {
        return $this->_varNameSort;
    }

    public function getVarNameDir()
    {
        return $this->_varNameDir;
    }

    public function getVarNameFilter()
    {
        return $this->_varNameFilter;
    }

    public function setVarNameLimit($name)
    {
        return $this->_varNameLimit = $name;
    }

    public function setVarNamePage($name)
    {
        return $this->_varNamePage = $name;
    }

    public function setVarNameSort($name)
    {
        return $this->_varNameSort = $name;
    }

    public function setVarNameDir($name)
    {
        return $this->_varNameDir = $name;
    }

    public function setVarNameFilter($name)
    {
        return $this->_varNameFilter = $name;
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

    public function setDefaultLimit($limit)
    {
        $this->_defaultLimit = $limit;
        return $this;
    }

    public function setDefaultPage($page)
    {
        $this->_defaultPage = $page;
        return $this;
    }

    public function setDefaultSort($sort)
    {
        $this->_defaultSort = $sort;
        return $this;
    }

    public function setDefaultDir($dir)
    {
        $this->_defaultDir = $dir;
        return $this;
    }

    public function setDefaultFilter($filter)
    {
        $this->_defaultFilter = $filter;
        return $this;
    }

    /**
     * Retrieve grid export types
     *
     * @return array
     */
    public function getExportTypes()
    {
        return empty($this->_exportTypes) ? false : $this->_exportTypes;
    }

    /**
     * Add new export type to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  Mage_Adminhtml_Block_Widget_Grid
     */
    public function addExportType($url, $label)
    {
        $this->_exportTypes[] = new Varien_Object(
            array(
                'url'   => Mage::getUrl($url, array('_current'=>true)),
                'label' => $label
            )
        );
        return $this;
    }

    /**
     * Retrieve grid HTML
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }

    /**
     * Retrieve grid as CSV
     *
     * @return unknown
     */
    public function getCsv()
    {
        $csv = '';
        $this->_prepareGrid();

        $data = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = $column->getHeader();
            }
        }
        $csv.= implode(';', $data)."\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = $this->getRowField($item, $column);
                }
            }
            $csv.= implode(';', $data)."\n";
        }
        return $csv;
    }

    public function getXml()
    {
        $this->_prepareGrid();
        $indexes = array();
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $indexes[] = $column->getIndex();
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($this->getCollection() as $item) {
            $xml.= $item->toXml($indexes);
        }
        $xml.= '</items>';
        return $xml;
    }

    public function canDisplayContainer()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            return false;
        }
        return true;
    }

    public function getGridUrl()
    {
        return $this->getCurrentUrl();
    }

    public function getParam($paramName, $default=null)
    {
        $session = Mage::getSingleton('adminhtml/session');
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

    public function setSaveParametersInSession($flag)
    {
        $this->_saveParametersInSession = $flag;
        return $this;
    }

    public function getJsObjectName()
    {
        return $this->getId().'JsObject';
    }
}

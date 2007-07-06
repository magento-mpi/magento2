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
    
    protected $_form = null;
    
    /**
     * Columns array
     *
     * array(
     *      'header'    => string,
     *      'width'     => int,
     *      'sortable'  => bool,
     *      'index'     => string,
     *      'renderer'  => Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface,
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
     * Default column item renderer
     *
     * @var Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface
     */
    protected $_gridItemRenderer = null;

    /**
     * Page and sorting var names
     *
     * @var string
     */
    protected $_varNameLimit    = 'limit';
    protected $_varNamePage     = 'page';
    protected $_varNameSort     = 'sort';
    protected $_varNameDir      = 'dir';

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
        $this->_gridItemRenderer = new Mage_Adminhtml_Block_Widget_Grid_Renderer();
    }

    /**
     * Get request object
     *
     * @return Mage_Core_Controller_Zend_Request
     */
    public function getRequest()
    {
        return $this->_request;
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
    
    public function getForm()
    {
        if (empty($this->_form)) {
            $this->_form = new Varien_Data_Form();
        }
        return $this->_form;
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
            $this->_columns[$columnId] = new Varien_Object($column);
        }
        elseif ($column instanceof Varien_Object) {
        	$this->_columns[$columnId] = $column;
        }
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
    
    /**
     * Retrieve column HTML properties
     *
     * @param   Varien_Object $column
     * @return  string
     */
    public function getColumnHtmlProperty(Varien_Object $column)
    {
        $out = ' ';
        if ($column->getWidth()) {
            $out='width="'.$column->getWidth().'%" ';
        }
        if ($column->getAlign()) {
            $out='align="'.$column->getAlign().'" ';
        }
        return $out;
    }
    
    /**
     * Retrieve column header HTML
     *
     * @param   Varien_Object $column
     * @return  string
     */
    public function getColumnHeaderHtml(Varien_Object $column)
    {
        $out = '';
        if ($column->getSortable()!==false) {

            $className = 'not-sort';
            $dir = (strtolower($column->getDir())=='asc') ? 'desc' : 'asc';
            if ($column->getDir()) {
                $className = 'sort-arrow-' . $dir;
            }
            $out = '<a href="" name="'.$column->getId().'" target="'.$dir
                   .'" class="' . $className . '">'.$column->getHeader().'</a>';
        }
        else {
            $out = $column->getHeader();
        }
        return $out;
    }
    
    /**
     * Set column filter
     *
     * @param   string || Varien_Object $column
     * @param   array $args
     * @return  Mage_Adminhtml_Block_Widget_Grid
     */
    public function setColumnFilter($column, $args=array())
    {
        if (is_string($column)) {
            $column = $this->getColumn($column);
        }
        if (!$column instanceof Varien_Object) {
            throw Mage::exception('Mage_Adminhtml', 'Invalid column specified');
        }
        if (empty($args['model'])) {
            $args['model'] = 'Varien_Data_Form_Element_Text';
        }

        $filter = Mage::getModel($args['model'], $args);
        
        $filter->setForm($this->getForm())
            ->setHtmlId('grid_filter_'.$column->getId())
            ->setName('grid_filter['.$column->getId().']');
            
        $column->setFilter($filter);
        
        return $this;
    }
    
    /**
     * Retrieve column filter HTML
     *
     * @param   Varien_Object $column
     * @return  string
     */
    public function getColumnFilterHtml(Varien_Object $column)
    {
        $out = '';
        
        $filter = $column->getFilter();
        if ($filter) {
            $out .= $filter->toHtml();
        }

        return $out;
    }
    
    /**
     * Prepare grid collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            $this->getCollection()->setPageSize($this->_request->getParam($this->getVarNameLimit(), 5));
            $this->getCollection()->setCurPage($this->_request->getParam($this->getVarNamePage(), 1));

            $columnId = $this->getRequest()->getParam($this->getVarNameSort(), false);
            $dir      = $this->getRequest()->getParam($this->getVarNameDir(), 'asc');

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->getCollection()->setOrder($this->_columns[$columnId]->getIndex(), $dir);
            }

            $this->getCollection()->load();
        }

        return $this;
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
    
    /**
     * Retrieve row column field value for display
     *
     * @param   Varien_Object $row
     * @param   Varien_Object $column
     * @return  string
     */
    public function getRowField(Varien_Object $row, Varien_Object $column)
    {
        $format = null;
        if ($column->getFormat() != '') {
            $format = $column->getFormat();
        }
        if (!($column->getRenderer() instanceof Mage_Adminhtml_Block_Widget_Grid_Renderer_Interface)) {
            // If no item rederer specified use default
            return $this->_gridItemRenderer->render($row, $column);
        } else {
            // If custom item renderer
            return $column->getRenderer()->render($row, $column);
        }
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

    public function setVarNameDir()
    {
        return $this->_varNameDir = $name;
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
}

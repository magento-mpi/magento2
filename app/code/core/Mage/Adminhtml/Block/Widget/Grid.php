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

    public function getColumn($columnId=null)
    {
        if (is_null($columnId)) {
            return $this->_columns;
        } elseif (!empty($this->_columns[$columnId])) {
            return $this->_columns[$columnId];
        }
        return false;
    }

    public function getColumnHtmlProperty($column)
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

    public function getColumnHeaderHtml($column)
    {
        $out = '';
        if ($column->getSortable()!==false) {

            $className = 'not-sort';
            $dir = (strtolower($column->getDir())=='asc') ? 'desc' : 'asc';
            if ($column->getDir()) {
                $className = 'sort-arrow-' . $dir;
            }
            $out = '<a href="" name="'.$column->getId().'" target="'.$dir.'" class="' . $className . '">'.$column->getHeader().'</a>';
        }
        else {
            $out = $column->getHeader();
        }
        return $out;
    }
    
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

        $model = Mage::getModel($args['model'], $args);
        $model->setForm($this->getForm())
            ->setHtmlId('grid_filter_'.$column->getId())
            ->setName('grid_filter['.$column->getId().']');
        $column->setFilter($model);
        
        return $this;
    }
    
    public function getColumnFilterHtml($column)
    {
        $out = '';
        
        $filter = $column->getFilter();
        if ($filter) {
            $out .= $filter->toHtml();
        }

        return $out;
    }

    protected function _beforeToHtml()
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

        $this->assign('collection', $this->getCollection());
        $this->assign('columns', $this->_columns);

        return $this;
    }

    public function getRowField(Varien_Object $row, Varien_Object $column)
    {
        //return $row->getData($column->getIndex());
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
    
}

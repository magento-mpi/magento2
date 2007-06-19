<?php
/**
 * Adminhtml grid widget block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
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
     *      'renderer'  => string ???
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

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/widget/grid.phtml');
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
    public function setCollection(Varien_Data_Collection $collection)
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
    
    public function getColumnHeader($column)
    {
        $out = '';
        if ($column->getSortable()!==false) {
            $dir = (strtolower($column->getDir())=='asc') ? 'desc' : 'asc';
            $out = '<a href="" name="'.$column->getId().'" target="'.$dir.'">'.$column->getHeader().'</a>';
        }
        else {
            $out = $column->getHeader();
        }
        return $out;
    }

    protected function _beforeToHtml()
    {
        if ($this->_collection) {
            $this->_collection->setPageSize($this->_request->getParam($this->getVarNameLimit(), 5));
            $this->_collection->setCurPage($this->_request->getParam($this->getVarNamePage(), 1));
            
            $columnId = $this->_request->getParam($this->getVarNameSort(), false);
            $dir      = $this->_request->getParam($this->getVarNameDir(), 'asc');
            
            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_collection->setOrder($this->_columns[$columnId]->getIndex(), $dir);
            }
            
            $this->_collection->load();
        }
        $this->assign('collection', $this->_collection);
        $this->assign('columns', $this->_columns);
        return $this;
    }

    public function getRowField(Varien_Object $row, Varien_Object $column)
    {
        return $row->getData($column->getIndex());
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
}
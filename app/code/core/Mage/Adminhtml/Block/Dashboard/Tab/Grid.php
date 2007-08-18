<?php
/**
 * Adminhtml dashboard grid with totals
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
 class Mage_Adminhtml_Block_Dashboard_Tab_Grid extends Mage_Adminhtml_Block_Dashboard_Tab_Abstract 
 {
 	/**
 	 * @see Mage_Adminhtml_Block_Widget_Grid
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

    protected $_lastColumnId;
 	
    protected $_totals = array();
    
 	/**
 	 * @see Mage_Adminhtml_Block_Widget_Grid
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
        $this->_lastColumnId = $columnId;
        return $this;
    }
    
    public function getRowValue($column, $row) 
    {
    	$field  = $column->getIndex();
    	if(is_array($row)) {
    		return $row[$field];
    	} else if($row instanceof Varien_Object) {
    		return $row->getData($field);
    	}
    	
    	return null;
    }
    
    public function addTotal($columnId, $labelText) 
    {
    	$this->_totals[$columnId] = $labelText;
    	return $this;
    }
 } // Class Mage_Adminhtml_Block_Dashboard_Tab_Grid end
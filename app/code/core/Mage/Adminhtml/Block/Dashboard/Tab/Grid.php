<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard grid with totals
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
            throw new Exception(__('Wrong column format'));
        }

        $this->_columns[$columnId]->setId($columnId);
        $this->_lastColumnId = $columnId;
        return $this;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getColumn($columnId)
    {
        return isset($this->_columns[$columnId]) ? $this->_columns[$columnId] : null;
    }


    public function getRowValue($column, $row)
    {
   	if(is_array($row)) {
    	       $row = new Varien_Object($row);
    	}

    	return $column->getRowField($row);
    }

    public function addTotal($columnId, $labelText)
    {
    	$this->_totals[$columnId] = $labelText;
    	return $this;
    }

    public function getTotals()
    {
        $result = array();
        foreach ($this->_totals as $index=>$label) {
            if($this->getColumn($index)) {
                $objIndex = $this->getColumn($index)->getIndex();
                $item = new Varien_Object(array($objIndex=>$this->getColumnSum($objIndex)));
                $result[] = array('label'=>$label, 'value'=>$this->getColumn($index)->getRowValue($item));
            }
        }
        return $result;
    }

    public function getColumnSum($index)
    {
        $sum = 0;
        $values = $this->getDataHelper()->getColumn($index);
        foreach ($values as $value) {
            $sum+= (float) $value;
        }

        return $sum;
    }

    public function getCount()
    {
        return sizeof($this->getDataHelper()->getItems());
    }

    protected function  _getTabTemplate()
    {
    	return 'dashboard/tab/grid.phtml';
    }
 } // Class Mage_Adminhtml_Block_Dashboard_Tab_Grid end

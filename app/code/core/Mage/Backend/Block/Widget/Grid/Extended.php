<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_Extended extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Columns array
     *
     * array(
     *      'header'    => string,
     *      'width'     => int,
     *      'sortable'  => bool,
     *      'index'     => string,
     *      //'renderer'  => Mage_Backend_Block_Widget_Grid_Column_Renderer_Interface,
     *      'format'    => string
     *      'total'     => string (sum, avg)
     * )
     * @var array
     */
    protected $_columns = array();

    /**
     * Identifier of last grid column
     *
     * @var string
     */
    protected $_lastColumnId;

    /**
     * Massaction row id field
     *
     * @var string
     */
    protected $_massactionIdField = null;

    /**
     * Massaction row id filter
     *
     * @var string
     */
    protected $_massactionIdFilter = null;

    /**
     * Massaction block name
     *
     * @var string
     */
    protected $_massactionBlockName = 'Mage_Backend_Block_Widget_Grid_Massaction';

    /**
     * Columns view order
     *
     * @var array
     */
    protected $_columnsOrder = array();

    /**
     * Label for empty cell
     *
     * @var string
     */
    protected $_emptyCellLabel = '';

    /**
     * Columns to group by
     *
     * @var array
     */
    protected $_groupedColumn = array();

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

    /*
    * @var boolean
    */
    public $_isCollapsed;

    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->setTemplate('Mage_Backend::widget/grid/extended.phtml');
        $this->_emptyText = Mage::helper('Mage_Backend_Helper_Data')->__('No records found.');
    }

    /**
     * Initialize child blocks
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('export_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Backend_Helper_Data')->__('Export'),
                    'onclick'   => $this->getJsObjectName().'.doExport()',
                    'class'   => 'task'
                ))
        );
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Backend_Helper_Data')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Mage_Backend_Helper_Data')->__('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'task'
                ))
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve column set block
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _getColumnSet()
    {
        if (!$this->getChildBlock('grid.columnSet')) {
            $this->setChild('grid.columnSet',
                $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_ColumnSet')
            );
        }
        return parent::_getColumnSet();
    }

    /**
     * Generate export button
     *
     * @return string
     */
    public function getExportButtonHtml()
    {
        return $this->getChildHtml('export_button');
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

    /**
     * Generate list of grid buttons
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if($this->getFilterVisibility()){
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        return $html;
    }

    /**
     * Add column to grid
     *
     * @param   string $columnId
     * @param   array || Varien_Object $column
     * @return  Mage_Backend_Block_Widget_Grid
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $this->_getColumnSet()->setChild(
                $columnId,
                $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Column')
                    ->setData($column)
                    ->setId($columnId)
                    ->setGrid($this)
            );
            $this->_getColumnSet()->getChildBlock($columnId)->setGrid($this);
        } else {
            throw new Exception(Mage::helper('Mage_Backend_Helper_Data')->__('Wrong column format.'));
        }

        $this->_lastColumnId = $columnId;
        return $this;
    }

    /**
     * Remove existing column
     *
     * @param string $columnId
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function removeColumn($columnId)
    {
        if ($this->_getColumnSet()->getChildBlock($columnId)) {
            $this->_getColumnSet()->unsetChild($columnId);
            if ($this->_lastColumnId == $columnId) {
                $this->_lastColumnId = array_pop($this->_getColumnSet()->getChildNames());
            }
        }
        return $this;
    }

    /**
     * Add column to grid after specified column.
     *
     * @param   string $columnId
     * @param   array|Varien_Object $column
     * @param   string $after
     * @return  Mage_Backend_Block_Widget_Grid
     */
    public function addColumnAfter($columnId, $column, $after)
    {
        $this->addColumn($columnId, $column);
        $this->addColumnsOrder($columnId, $after);
        return $this;
    }

    /**
     * Add column view order
     *
     * @param string $columnId
     * @param string $after
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function addColumnsOrder($columnId, $after)
    {
        $this->_columnsOrder[$columnId] = $after;
        return $this;
    }

    /**
     * Retrieve columns order
     *
     * @return array
     */
    public function getColumnsOrder()
    {
        return $this->_columnsOrder;
    }

    /**
     * Sort columns by predefined order
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function sortColumnsByOrder()
    {
        $keys = array_keys($this->_columns);
        $values = array_values($this->_columns);

        foreach ($this->getColumnsOrder() as $columnId => $after) {
            $this->getLayout()->reorderChild(
                $this->_getColumnSet()->getNameInLayout(),
                $columnId,
                $after
            );
        }

        $this->_lastColumnId = array_pop($this->_getColumnSet()->getChildNames());
        return $this;
    }

    /**
     * Retrieve identifier of last column
     *
     * @return string
     */
    public function getLastColumnId()
    {
        return $this->_lastColumnId;
    }

    /**
     * Initialize grid columns
     *
     * @return Mage_Backend_Block_Widget_Grid_Extended
     */
    protected function _prepareColumns()
    {
        $this->sortColumnsByOrder();
        return $this;
    }

    /**
     * Prepare grid massaction block
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareMassactionBlock()
    {
        $this->setChild('massaction', $this->getLayout()->createBlock($this->getMassactionBlockName()));
        $this->_prepareMassaction();
        if($this->getMassactionBlock()->isAvailable()) {
            $this->_prepareMassactionColumn();
        }
        return $this;
    }

    /**
     * Prepare grid massaction actions
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare grid massaction column
     *
     * @return Mage_Backend_Block_Widget_Grid_Extended
     */
    protected function _prepareMassactionColumn()
    {
        $columnId = 'massaction';
        $massactionColumn = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Column')
                ->setData(array(
                    'index'        => $this->getMassactionIdField(),
                    'filter_index' => $this->getMassactionIdFilter(),
                    'type'         => 'massaction',
                    'name'         => $this->getMassactionBlock()->getFormFieldName(),
                    'align'        => 'center',
                    'is_system'    => true
                ));

        if ($this->getNoFilterMassactionColumn()) {
            $massactionColumn->setData('filter', false);
        }

        $massactionColumn->setSelected($this->getMassactionBlock()->getSelected())
            ->setGrid($this)
            ->setId($columnId);

        $this->_getColumnSet()->setChild($columnId, $massactionColumn);
        return $this;
    }

    /**
     * Initialize grid before rendering
     *
     * @return Mage_Backend_Block_Widget_Grid_Extended|void
     */
    protected function _prepareGrid()
    {
        $this->_prepareColumns();
        $this->_prepareMassactionBlock();
        parent::_prepareGrid();
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
     * Retrieve massaction row identifier field
     *
     * @return string
     */
    public function getMassactionIdField()
    {
        return $this->_massactionIdField;
    }

    /**
     * Set massaction row identifier field
     *
     * @param  string    $idField
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setMassactionIdField($idField)
    {
        $this->_massactionIdField = $idField;
        return $this;
    }

    /**
     * Retrieve massaction row identifier filter
     *
     * @return string
     */
    public function getMassactionIdFilter()
    {
        return $this->_massactionIdFilter;
    }

    /**
     * Set massaction row identifier filter
     *
     * @param string $idFilter
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setMassactionIdFilter($idFilter)
    {
        $this->_massactionIdFilter = $idFilter;
        return $this;
    }

    /**
     * Retrive massaction block name
     *
     * @return string
     */
    public function getMassactionBlockName()
    {
        return $this->_massactionBlockName;
    }

    /**
     * Set massaction block name
     *
     * @param  string    $blockName
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setMassactionBlockName($blockName)
    {
        $this->_massactionBlockName = $blockName;
        return $this;
    }

    /**
     * Retrive massaction block
     *
     * @return Mage_Backend_Block_Widget_Grid_Massaction_Abstract
     */
    public function getMassactionBlock()
    {
        return $this->getChildBlock('massaction');
    }

    /**
     * Generate massaction block
     *
     * @return string
     */
    public function getMassactionBlockHtml()
    {
        return $this->getChildHtml('massaction');
    }

    /**
     * Retrieve columns to render
     *
     * @return array
     */
    public function getSubTotalColumns() {
        return $this->getColumns();
    }

    /**
     * Check whether should render cell
     *
     * @param Varien_Object $item
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return boolean
     */
    public function shouldRenderCell($item, $column)
    {
        if ($this->isColumnGrouped($column) && $item->getIsEmpty()) {
            return true;
        }
        if (!$item->getIsEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve label for empty cell
     *
     * @return string
     */
    public function getEmptyCellLabel()
    {
        return $this->_emptyCellLabel;
    }

    /**
     * Set label for empty cell
     *
     * @param string $label
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function setEmptyCellLabel($label)
    {
        $this->_emptyCellLabel = $label;
        return $this;
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
     * Count columns
     *
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
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
     * Set flag whether is collapsed
     * @param $isCollapsed
     * @return Mage_Backend_Block_Widget_Grid_ColumnSet
     */
    public function setIsCollapsed($isCollapsed)
    {
        $this->_isCollapsed = $isCollapsed;
        return $this;
    }

    /**
     * Retrieve flag is collapsed
     * @return mixed
     */
    public function getIsCollapsed()
    {
        return $this->_isCollapsed;
    }
}

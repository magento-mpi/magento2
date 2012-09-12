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
     * Grid export types
     *
     * @var array
     */
    protected $_exportTypes = array();

    /**
     * Rows per page for import
     *
     * @var int
     */
    protected $_exportPageSize = 1000;

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
     * Export flag
     *
     * @var bool
     */
    protected $_isExport = false;

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
    public function getColumnSet()
    {
        if (!$this->getChildBlock('grid.columnSet')) {
            $this->setChild('grid.columnSet',
                $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_ColumnSet')
            );
        }
        return parent::getColumnSet();
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
     * Add new export type to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  Mage_Backend_Block_Widget_Grid
     */
    public function addExportType($url, $label)
    {
        $this->_exportTypes[] = new Varien_Object(
            array(
                'url'   => $this->getUrl($url, array('_current'=>true)),
                'label' => $label
            )
        );
        return $this;
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
            $this->getColumnSet()->setChild(
                $columnId,
                $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Column')
                    ->setData($column)
                    ->setId($columnId)
            );
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
        if ($this->getColumnSet()->getChildBlock($columnId)) {
            $this->getColumnSet()->unsetChild($columnId);
            if ($this->_lastColumnId == $columnId) {
                $this->_lastColumnId = array_pop($this->getColumnSet()->getChildNames());
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
        foreach ($this->getColumnsOrder() as $columnId => $after) {
            $this->getLayout()->reorderChild(
                $this->getColumnSet()->getNameInLayout(),
                $this->getColumn($columnId)->getNameInLayout(),
                $this->getColumn($after)->getNameInLayout()
            );
        }

        $columns = $this->getColumnSet()->getChildNames();
        $this->_lastColumnId = array_pop($columns);
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

        $this->getColumnSet()->setChild($columnId, $massactionColumn);
        return $this;
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            parent::_prepareCollection();

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    /**
     * Process collection after loading
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _afterLoadCollection()
    {
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
     * Retrieve file content from file container array
     *
     * @param array $fileData
     * @return string
     */
    protected function _getFileContainerContent(array $fileData)
    {
        $io = new Varien_Io_File();
        $path = $io->dirname($fileData['value']);
        $io->open(array('path' => $path));
        return $io->read($fileData['value']);
    }

    /**
     * Retrieve Headers row array for Export
     *
     * @return array
     */
    protected function _getExportHeaders()
    {
        $row = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getExportHeader();
            }
        }
        return $row;
    }

    /**
     * Retrieve Totals row array for Export
     *
     * @return array
     */
    protected function _getExportTotals()
    {
        $totals = $this->getTotals();
        $row    = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $row[] = ($column->hasTotalsLabel()) ? $column->getTotalsLabel() : $column->getRowFieldExport($totals);
            }
        }
        return $row;
    }

    /**
     * Iterate collection and call callback method per item
     * For callback method first argument always is item object
     *
     * @param string $callback
     * @param array $args additional arguments for callback method
     * @return Mage_Backend_Block_Widget_Grid
     */
    public function _exportIterateCollection($callback, array $args)
    {
        $originalCollection = $this->getCollection();
        $count = null;
        $page  = 1;
        $lPage = null;
        $break = false;

        while ($break !== true) {
            $collection = clone $originalCollection;
            $collection->setPageSize($this->_exportPageSize);
            $collection->setCurPage($page);
            $collection->load();
            if (is_null($count)) {
                $count = $collection->getSize();
                $lPage = $collection->getLastPageNumber();
            }
            if ($lPage == $page) {
                $break = true;
            }
            $page ++;

            foreach ($collection as $item) {
                call_user_func_array(array($this, $callback), array_merge(array($item), $args));
            }
        }
    }

    /**
     * Write item data to csv export file
     *
     * @param Varien_Object $item
     * @param Varien_Io_File $adapter
     */
    protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter)
    {
        $row = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }
        $adapter->streamWriteCsv($row);
    }

    /**
     * Retrieve a file container array by grid data as CSV
     *
     * Return array with keys type and value
     *
     * @return array
     */
    public function getCsvFile()
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getExportHeaders());

        $this->_exportIterateCollection('_exportCsvItem', array($io));

        if ($this->getCountTotals()) {
            $io->streamWriteCsv($this->_getExportTotals());
        }

        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }

    /**
     * Retrieve Grid data as CSV
     *
     * @return string
     */
    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach ($this->getCollection() as $item) {
            $data = array();
            foreach ($this->getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($item)) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        if ($this->getCountTotals())
        {
            $data = array();
            foreach ($this->getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($this->getTotals())) . '"';
                }
            }
            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

    /**
     * Retrieve data in xml
     *
     * @return string
     */
    public function getXml()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $indexes = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $indexes[] = $column->getIndex();
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($this->getCollection() as $item) {
            $xml.= $item->toXml($indexes);
        }
        if ($this->getCountTotals())
        {
            $xml.= $this->getTotals()->toXml($indexes);
        }
        $xml.= '</items>';
        return $xml;
    }

    /**
     *  Get a row data of the particular columns
     *
     * @param Varien_Object $data
     * @return array
     */
    public function getRowRecord(Varien_Object $data)
    {
        $row = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($data);
            }
        }
        return $row;
    }

    /**
     * Retrieve a file container array by grid data as MS Excel 2003 XML Document
     *
     * Return array with keys type and value
     *
     * @param string $sheetName
     * @return array
     */
    public function getExcelFile($sheetName = '')
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        $convert    = new Magento_Convert_Excel($this->getCollection()->getIterator(), array($this, 'getRowRecord'));
        $io      = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.xml';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $convert->setDataHeader($this->_getExportHeaders());
        if ($this->getCountTotals()) {
            $convert->setDataFooter($this->_getExportTotals());
        }

        $convert->write($io, $sheetName);
        $io->streamUnlock();
        $io->streamClose();

        return array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        );
    }

    /**
     * Retrieve grid data as MS Excel 2003 XML Document
     *
     * @return string
     */
    public function getExcel()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $headers = array();
        $data = array();
        foreach ($this->getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $headers[] = $column->getHeader();
            }
        }
        $data[] = $headers;

        foreach ($this->getCollection() as $item) {
            $row = array();
            foreach ($this->getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($item);
                }
            }
            $data[] = $row;
        }

        if ($this->getCountTotals())
        {
            $row = array();
            foreach ($this->getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($this->getTotals());
                }
            }
            $data[] = $row;
        }

        $convert = new Magento_Convert_Excel(new ArrayIterator($data));
        return $convert->convert('single_sheet');
    }
}

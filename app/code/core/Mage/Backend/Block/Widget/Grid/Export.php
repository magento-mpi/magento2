<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_Export extends Mage_Backend_Block_Widget
{
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
     * Retrieve grid columns
     *
     * @return Mage_Backend_Block_Widget_Grid_Column[]
     */
    protected function _getColumns()
    {
        return $this->getParentBlock()->getColumns();
    }

    /**
     * Retrieve totals
     *
     * @return Varien_Object
     */
    protected function _getTotals()
    {
        return $this->getParentBlock()->getTotals();
    }

    /**
     * Return count totals
     *
     * @return bool
     */
    protected function getCountTotals()
    {
        return $this->getParentBlock()->getCountTotals();
    }

    /**
     * Get collection object
     *
     * @return Varien_Data_Collection
     */
    protected function _getCollection()
    {
        return $this->getParentBlock()->getCollection();
    }

    /**
     * Retrieve grid export types
     *
     * @return array|bool
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
        foreach ($this->_getColumns() as $column) {
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
        $totals = $this->_getTotals();
        $row    = array();
        foreach ($this->_getColumns() as $column) {
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
        $originalCollection = $this->getParentBlock()->getPreparedCollection();
        $count = null;
        $page  = 1;
        $lPage = null;
        $break = false;

        while ($break !== true) {
            $collection = clone $originalCollection;
            $collection->setPageSize($this->getExportPageSize());
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
        foreach ($this->_getColumns() as $column) {
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
        $collection = $this->getParentBlock()->getPreparedCollection();
        $collection->getSelect()->limit();
        $collection->setPageSize(0);
        $collection->load();

        $data = array();
        foreach ($this->_getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"'.$column->getExportHeader().'"';
            }
        }
        $csv.= implode(',', $data)."\n";

        foreach ($collection as $item) {
            $data = array();
            foreach ($this->_getColumns() as $column) {
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
            foreach ($this->_getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(array('"', '\\'), array('""', '\\\\'),
                        $column->getRowFieldExport($this->_getTotals())) . '"';
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
        $collection = $this->getParentBlock()->getPreparedCollection();
        $collection->getSelect()->limit();
        $collection->setPageSize(0);
        $collection->load();

        $indexes = array();
        foreach ($this->_getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $indexes[] = $column->getIndex();
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<items>';
        foreach ($collection as $item) {
            $xml .= $item->toXml($indexes);
        }
        if ($this->getCountTotals())
        {
            $xml .= $this->_getTotals()->toXml($indexes);
        }
        $xml .= '</items>';
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
        foreach ($this->_getColumns() as $column) {
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
        $collection = $this->getParentBlock()->getPreparedCollection();
        $collection->load();

        $convert    = new Magento_Convert_Excel($collection->getIterator(), array($this, 'getRowRecord'));
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
        $collection = $this->getParentBlock()->getPreparedCollection();
        $collection->getSelect()->limit();
        $collection->setPageSize(0);
        $collection->load();

        $headers = array();
        $data = array();
        foreach ($this->_getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $headers[] = $column->getHeader();
            }
        }
        $data[] = $headers;

        foreach ($collection as $item) {
            $row = array();
            foreach ($this->_getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($item);
                }
            }
            $data[] = $row;
        }

        if ($this->getCountTotals())
        {
            $row = array();
            foreach ($this->_getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($this->_getTotals());
                }
            }
            $data[] = $row;
        }

        $convert = new Magento_Convert_Excel(new ArrayIterator($data));
        return $convert->convert('single_sheet');
    }
}

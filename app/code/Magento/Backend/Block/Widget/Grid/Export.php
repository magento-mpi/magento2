<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Export extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Grid\ExportInterface
{
    /**
     * Grid export types
     *
     * @var  \Magento\Object[]
     */
    protected $_exportTypes = array();

    /**
     * Rows per page for import
     *
     * @var int
     */
    protected $_exportPageSize = 1000;

    /**
     * Template file name
     *
     * @var string
     */
    protected $_template = "Magento_Backend::widget/grid/export.phtml";

    /**
     * @var \Magento\Data\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_directory;

    /**
     * Additional path to folder
     *
     * @var string
     */
    protected $_path = 'export';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Data\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Data\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     * @throws \Magento\Core\Exception
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->hasData('exportTypes')) {
            foreach ($this->getData('exportTypes') as $type) {
                if (!isset($type['urlPath']) || !isset($type['label'])) {
                    throw new \Magento\Core\Exception('Invalid export type supplied for grid export block');
                }
                $this->addExportType($type['urlPath'], $type['label']);
            }
        }
        $this->_directory = $this->_filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Retrieve grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Column[]
     */
    protected function _getColumns()
    {
        return $this->getParentBlock()->getColumns();
    }

    /**
     * Retrieve totals
     *
     * @return \Magento\Object
     */
    protected function _getTotals()
    {
        return $this->getParentBlock()->getColumnSet()->getTotals();
    }

    /**
     * Return count totals
     *
     * @return bool
     */
    public function getCountTotals()
    {
        return $this->getParentBlock()->getColumnSet()->shouldRenderTotal();
    }

    /**
     * Get collection object
     *
     * @return \Magento\Data\Collection
     */
    protected function _getCollection()
    {
        return $this->getParentBlock()->getCollection();
    }

    /**
     * Retrieve grid export types
     *
     * @return  \Magento\Object[]|false
     */
    public function getExportTypes()
    {
        return empty($this->_exportTypes) ? false : $this->_exportTypes;
    }

    /**
     * Retrieve grid id
     *
     * @return string
     */
    public function getId()
    {
        return $this->getParentBlock()->getId();
    }

    /**
     * Prepare export button
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'export_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                array(
                    'label' => __('Export'),
                    'onclick' => $this->getParentBlock()->getJsObjectName() . '.doExport()',
                    'class' => 'task'
                )
            )
        );
        return parent::_prepareLayout();
    }

    /**
     * Render export button
     *
     * @return string
     */
    public function getExportButtonHtml()
    {
        return $this->getChildHtml('export_button');
    }

    /**
     * Add new export type to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  $this
     */
    public function addExportType($url, $label)
    {
        $this->_exportTypes[] = new \Magento\Object(
            array('url' => $this->getUrl($url, array('_current' => true)), 'label' => $label)
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
        return $this->_directory->readFile('export/' . $fileData['value']);
    }

    /**
     * Retrieve Headers row array for Export
     *
     * @return string[]
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
     * @return string[]
     */
    protected function _getExportTotals()
    {
        $totals = $this->_getTotals();
        $row = array();
        foreach ($this->_getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->hasTotalsLabel() ? $column->getTotalsLabel() : $column->getRowFieldExport($totals);
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
     * @return void
     */
    public function _exportIterateCollection($callback, array $args)
    {
        /** @var $originalCollection \Magento\Data\Collection */
        $originalCollection = $this->getParentBlock()->getPreparedCollection();
        $count = null;
        $page = 1;
        $lPage = null;
        $break = false;

        while ($break !== true) {
            $originalCollection->setPageSize($this->getExportPageSize());
            $originalCollection->setCurPage($page);
            $originalCollection->load();
            if (is_null($count)) {
                $count = $originalCollection->getSize();
                $lPage = $originalCollection->getLastPageNumber();
            }
            if ($lPage == $page) {
                $break = true;
            }
            $page++;

            $collection = $this->_getRowCollection($originalCollection);
            foreach ($collection as $item) {
                call_user_func_array(array($this, $callback), array_merge(array($item), $args));
            }
        }
    }

    /**
     * Write item data to csv export file
     *
     * @param \Magento\Object $item
     * @param \Magento\Filesystem\File\WriteInterface $stream
     * @return void
     */
    protected function _exportCsvItem(\Magento\Object $item, \Magento\Filesystem\File\WriteInterface $stream)
    {
        $row = array();
        foreach ($this->_getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }
        $stream->writeCsv($row);
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
        $name = md5(microtime());
        $file = $this->_path . '/' . $name . '.csv';

        $this->_directory->create($this->_path);
        $stream = $this->_directory->openFile($file, 'w+');

        $stream->writeCsv($this->_getExportHeaders());
        $stream->lock();
        $this->_exportIterateCollection('_exportCsvItem', array($stream));
        if ($this->getCountTotals()) {
            $stream->writeCsv($this->_getExportTotals());
        }
        $stream->unlock();
        $stream->close();

        return array('type' => 'filename', 'value' => $file, 'rm' => true);
    }

    /**
     * Retrieve Grid data as CSV
     *
     * @return string
     */
    public function getCsv()
    {
        $csv = '';
        $collection = $this->_getPreparedCollection();

        $data = array();
        foreach ($this->_getColumns() as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"' . $column->getExportHeader() . '"';
            }
        }
        $csv .= implode(',', $data) . "\n";

        foreach ($collection as $item) {
            $data = array();
            foreach ($this->_getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(
                        array('"', '\\'),
                        array('""', '\\\\'),
                        $column->getRowFieldExport($item)
                    ) . '"';
                }
            }
            $csv .= implode(',', $data) . "\n";
        }

        if ($this->getCountTotals()) {
            $data = array();
            foreach ($this->_getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(
                        array('"', '\\'),
                        array('""', '\\\\'),
                        $column->getRowFieldExport($this->_getTotals())
                    ) . '"';
                }
            }
            $csv .= implode(',', $data) . "\n";
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
        $collection = $this->_getPreparedCollection();

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
        if ($this->getCountTotals()) {
            $xml .= $this->_getTotals()->toXml($indexes);
        }
        $xml .= '</items>';
        return $xml;
    }

    /**
     *  Get a row data of the particular columns
     *
     * @param \Magento\Object $data
     * @return string[]
     */
    public function getRowRecord(\Magento\Object $data)
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
        $collection = $this->_getRowCollection();

        $convert = new \Magento\Convert\Excel($collection->getIterator(), array($this, 'getRowRecord'));

        $name = md5(microtime());
        $file = $this->_path . '/' . $name . '.xml';

        $this->_directory->create($this->_path);
        $stream = $this->_directory->openFile($file, 'w+');
        $stream->lock();

        $convert->setDataHeader($this->_getExportHeaders());
        if ($this->getCountTotals()) {
            $convert->setDataFooter($this->_getExportTotals());
        }

        $convert->write($stream, $sheetName);
        $stream->unlock();
        $stream->close();

        return array('type' => 'filename', 'value' => $file, 'rm' => true);
    }

    /**
     * Retrieve grid data as MS Excel 2003 XML Document
     *
     * @return string
     */
    public function getExcel()
    {
        $collection = $this->_getPreparedCollection();

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

        if ($this->getCountTotals()) {
            $row = array();
            foreach ($this->_getColumns() as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($this->_getTotals());
                }
            }
            $data[] = $row;
        }

        $convert = new \Magento\Convert\Excel(new \ArrayIterator($data));
        return $convert->convert('single_sheet');
    }

    /**
     * Reformat base collection into collection without sub-collection in items
     *
     * @param \Magento\Data\Collection $baseCollection
     * @return \Magento\Data\Collection
     */
    protected function _getRowCollection(\Magento\Data\Collection $baseCollection = null)
    {
        if (null === $baseCollection) {
            $baseCollection = $this->getParentBlock()->getPreparedCollection();
        }
        $collection = $this->_collectionFactory->create();

        /** @var $item \Magento\Object */
        foreach ($baseCollection as $item) {
            if ($item->getIsEmpty()) {
                continue;
            }
            if ($item->hasChildren() && count($item->getChildren()) > 0) {
                /** @var $subItem \Magento\Object */
                foreach ($item->getChildren() as $subItem) {
                    $tmpItem = clone $item;
                    $tmpItem->unsChildren();
                    $tmpItem->addData($subItem->getData());
                    $collection->addItem($tmpItem);
                }
            } else {
                $collection->addItem($item);
            }
        }

        return $collection;
    }

    /**
     * Return prepared collection as row collection with additional conditions
     *
     * @return \Magento\Data\Collection
     */
    public function _getPreparedCollection()
    {
        /** @var $collection \Magento\Data\Collection */
        $collection = $this->getParentBlock()->getPreparedCollection();
        $collection->setPageSize(0);
        $collection->load();

        return $this->_getRowCollection($collection);
    }

    /**
     * @return int
     */
    public function getExportPageSize()
    {
        return $this->_exportPageSize;
    }
}

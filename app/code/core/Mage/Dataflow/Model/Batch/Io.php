<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Dataflow batch Io model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Batch_Io
{
    const TMP_DIR = '/var/tmp/';
    const TMP_NAME = 'batch_%d.tmp';

    /**
     * Dataflow Batch model
     *
     * @var Mage_Dataflow_Model_Batch
     */
    protected $_batchModel;

    /**
     * Full path to tmp dir
     *
     * @var string
     */
    protected $_path;

    /**
     * Filename
     *
     * @var string
     */
    protected $_filename;

    /**
     * Varien IO File class
     *
     * @var Varien_Io_File
     */
    protected $_ioFile;

    /**
     * size of file
     *
     * @var int
     */
    protected $_fileSize = 0;

    /**
     * Init model (required)
     *
     * @param Mage_Dataflow_Model_Batch $object
     * @return Mage_Dataflow_Model_Batch_Io
     */
    public function init(Mage_Dataflow_Model_Batch $object)
    {
        $this->_batchModel = $object;
        return $this;
    }

    /**
     * Retrieve real path to tmp dir
     *
     * @return string
     */
    public function getPath()
    {
        if (is_null($this->_path)) {
            $this->_path = $this->getIoAdapter()->getCleanPath(Mage::getBaseDir('tmp'));
            $this->getIoAdapter()->checkAndCreateFolder($this->_path);
        }
        return $this->_path;
    }

    /**
     * Retrieve tmp filename
     *
     * @return string
     */
    public function getFile($withPath = false)
    {
        if (is_null($this->_filename)) {
            $this->_filename = sprintf(self::TMP_NAME, $this->_batchModel->getId());
        }
        if ($withPath) {
            return $this->getPath() . $this->_filename;
        }
        return $this->_filename;
    }

    /**
     * Retrieve Io File Adapter
     *
     * @return Varien_Io_File
     */
    public function getIoAdapter()
    {
        if (is_null($this->_ioFile)) {
            $this->_ioFile = new Varien_Io_File();
        }
        return $this->_ioFile;
    }

    /**
     * Open file in stream mode
     *
     * @return Mage_Dataflow_Model_Batch_Io
     */
    public function open($write = true)
    {
        $mode = $write ? 'w+' : 'r+';
        $ioConfig = array(
            'path' => $this->getPath()
        );
        $this->getIoAdapter()->setAllowCreateFolders(true);
        $this->getIoAdapter()->open($ioConfig);
        $this->getIoAdapter()->streamOpen($this->getFile(), $mode);

        $this->_fileSize = 0;

        return $this;
    }

    /**
     * Write string
     *
     * @param string $string
     * @return bool
     */
    public function write($string)
    {
        $this->_fileSize += strlen($string);
        return $this->getIoAdapter()->streamWrite($string);
    }

    /**
     * Read up to 1K bytes from the file pointer
     * Reading stops as soon as one of the following conditions is met:
     * # length  bytes have been read
     * # EOF (end of file) is reached
     *
     * @return string|array
     */
    public function read($csv = false, $delimiter = ',', $enclosure = '"')
    {
        if ($csv) {
            $content = $this->getIoAdapter()->streamReadCsv($delimiter, $enclosure);
        }
        else {
            $content = $this->getIoAdapter()->streamRead(1024);
            $this->_fileSize += strlen($content);
        }
        return $content;
    }

    /**
     * Close file
     *
     * @return bool
     */
    public function close()
    {
        return $this->getIoAdapter()->streamClose();
    }

    public function clear()
    {
        return $this->getIoAdapter()->rm($this->getFile(true));
    }

    /**
     * Get writed file size
     *
     * @return unknown
     */
    public function getFileSize()
    {
        return $this->_fileSize;
    }
}

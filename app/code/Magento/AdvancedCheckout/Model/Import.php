<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\Exception;

/**
 * Import data from file
 *
 */
class Import extends \Magento\Framework\Object
{
    /**
     * Form field name
     */
    const FIELD_NAME_SOURCE_FILE = 'sku_file';

    /**
     * Uploaded file name
     *
     * @var string
     */
    protected $_uploadedFile = '';

    /**
     * Allowed file name extensions to upload
     *
     * @var string[]
     */
    protected $_allowedExtensions = array('csv');

    /**
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * File uploader factory
     *
     * @var \Magento\Core\Model\File\UploaderFactory
     */
    protected $_uploaderFactory = null;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $varDirectory;

    /**
     * Upload path
     *
     * @var string
     */
    protected $uploadPath = 'import_sku/';

    /**
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param \Magento\Core\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        \Magento\Core\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\App\Filesystem $filesystem,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($data);
        $this->_uploaderFactory = $uploaderFactory;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * Destructor, removes uploaded file
     */
    public function __destruct()
    {
        if (!empty($this->_uploadedFile)) {
            $this->varDirectory->delete($this->_uploadedFile);
        }
    }

    /**
     * Upload file
     *
     * @throws Exception
     * @return void
     */
    public function uploadFile()
    {
        /** @var $uploader \Magento\Core\Model\File\Uploader */
        $uploader = $this->_uploaderFactory->create(array('fileId' => self::FIELD_NAME_SOURCE_FILE));
        $uploader->setAllowedExtensions($this->_allowedExtensions);
        $uploader->skipDbProcessing(true);
        if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
            throw new Exception($this->_getFileTypeMessageText());
        }

        try {
            $result = $uploader->save($this->varDirectory->getAbsolutePath($this->uploadPath));
            $this->_uploadedFile = $this->varDirectory->getRelativePath($result['path'] . $result['file']);
        } catch (\Exception $e) {
            throw new Exception($this->_checkoutData->getFileGeneralErrorText());
        }
    }

    /**
     * Get rows from file
     *
     * @return array
     * @throws Exception
     */
    public function getRows()
    {
        $extension = pathinfo($this->_uploadedFile, PATHINFO_EXTENSION);
        $method = $this->_getMethodByExtension(strtolower($extension));
        if (!empty($method) && is_callable([$this, $method])) {
            return $this->{$method}();
        }

        throw new \Magento\Framework\Model\Exception($this->_getFileTypeMessageText());
    }

    /**
     * Get rows from CSV file
     *
     * @return array
     * @throws Exception
     */
    public function getDataFromCsv()
    {
        if (!$this->_uploadedFile || !$this->varDirectory->isExist($this->_uploadedFile)) {
            throw new Exception($this->_checkoutData->getFileGeneralErrorText());
        }

        $csvData = array();

        try {
            $fileHandler = $this->varDirectory->openFile($this->_uploadedFile, 'r');
            if ($fileHandler) {
                $colNames = $fileHandler->readCsv();

                foreach ($colNames as &$colName) {
                    $colName = trim($colName);
                }

                $requiredColumns = array('sku', 'qty');
                $requiredColumnsPositions = array();

                foreach ($requiredColumns as $columnName) {
                    $found = array_search($columnName, $colNames);
                    if (false !== $found) {
                        $requiredColumnsPositions[] = $found;
                    } else {
                        throw new Exception($this->_checkoutData->getSkuEmptyDataMessageText());
                    }
                }

                while (($currentRow = $fileHandler->readCsv()) !== false) {
                    $csvDataRow = array('qty' => '');
                    foreach ($requiredColumnsPositions as $index) {
                        if (isset($currentRow[$index])) {
                            $csvDataRow[$colNames[$index]] = trim($currentRow[$index]);
                        }
                    }
                    if (isset($csvDataRow['sku']) && $csvDataRow['sku'] !== '') {
                        $csvData[] = $csvDataRow;
                    }
                }
                $fileHandler->close();
            }
        } catch (\Exception $e) {
            throw new Exception(__('The file is corrupt.'));
        }
        return $csvData;
    }

    /**
     * Get Method to load data by file extension
     *
     * @param string $extension
     * @return string
     * @throws Exception
     */
    protected function _getMethodByExtension($extension)
    {
        foreach ($this->_allowedExtensions as $allowedExtension) {
            if ($allowedExtension == $extension) {
                return 'getDataFrom' . ucfirst($allowedExtension);
            }
        }

        throw new Exception($this->_getFileTypeMessageText());
    }

    /**
     * Get message text of wrong file type error
     *
     * @return string
     */
    protected function _getFileTypeMessageText()
    {
        return __('This file needs to be in .csv format.');
    }
}

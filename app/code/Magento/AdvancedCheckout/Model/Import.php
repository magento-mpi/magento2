<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import data from file
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
class Magento_AdvancedCheckout_Model_Import extends Magento_Object
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
     * @var array
     */
    protected $_allowedExtensions = array(
        'csv'
    );

    /**
     * @var Magento_AdvancedCheckout_Helper_Data
     */
    protected $_checkoutData = null;

    /**
     * File uploader factory
     *
     * @var Magento_Core_Model_File_UploaderFactory
     */
    protected $_uploaderFactory = null;

    /**
     * @var Magento_Core_Model_Dir
     */
    protected $_dir = null;

    public function __construct(
        Magento_AdvancedCheckout_Helper_Data $checkoutData,
        Magento_Core_Model_File_UploaderFactory $uploaderFactory,
        Magento_Core_Model_Dir $dir,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($data);
        $this->_uploaderFactory = $uploaderFactory;
        $this->_dir = $dir;
    }

    /**
     * Destructor, removes uploaded file
     */
    public function __destruct()
    {
        if (!empty($this->_uploadedFile)) {
            unlink($this->_uploadedFile);
        }
    }

    /**
     * Upload file
     *
     * @throws Magento_Core_Exception
     * @return void
     */
    public function uploadFile()
    {
        /** @var $uploader Magento_Core_Model_File_Uploader */
        $uploader  = $this->_uploaderFactory->create(array('fileId' => self::FIELD_NAME_SOURCE_FILE));
        $uploader->setAllowedExtensions($this->_allowedExtensions);
        $uploader->skipDbProcessing(true);
        if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
            throw new Magento_Core_Exception($this->_getFileTypeMessageText());
        }

        try {
            $result = $uploader->save($this->_getWorkingDir());
            $this->_uploadedFile = $result['path'] . $result['file'];
        } catch (Exception $e) {
            throw new Magento_Core_Exception(
                $this->_checkoutData->getFileGeneralErrorText()
            );
        }
    }

    /**
     * Get rows from file
     *
     * @throws Magento_Core_Exception
     * @return array
     */
    public function getRows()
    {
        $extension = pathinfo($this->_uploadedFile, PATHINFO_EXTENSION);
        $method = $this->_getMethodByExtension(strtolower($extension));
        if (!empty($method) && method_exists($this, $method)) {
            return $this->$method();
        }

        throw new Magento_Core_Exception($this->_getFileTypeMessageText());
    }

    /**
     * Get rows from CSV file
     *
     * @throws Magento_Core_Exception
     * @return array
     */
    public function getDataFromCsv()
    {
        if (!$this->_uploadedFile || !file_exists($this->_uploadedFile)) {
            throw new Magento_Core_Exception(
                $this->_checkoutData->getFileGeneralErrorText()
            );
        }

        $csvData = array();

        try {
            $fileHandler = fopen($this->_uploadedFile, 'r');
            if ($fileHandler) {
                $colNames = fgetcsv($fileHandler);

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
                        throw new Magento_Core_Exception(
                            $this->_checkoutData->getSkuEmptyDataMessageText()
                        );
                    }
                }

                while (($currentRow = fgetcsv($fileHandler)) !== false) {
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
                fclose($fileHandler);
            }
        } catch (Exception $e) {
            throw new Magento_Core_Exception(__('The file is corrupt.'));
        }
        return $csvData;
    }

    /**
     * Import SKU working directory
     *
     * @return string
     */
    protected function _getWorkingDir()
    {
        return $this->_dir->getDir('var') . DS . 'import_sku' . DS;
    }

    /**
     * Get Method to load data by file extension
     *
     * @param string $extension
     * @throws Magento_Core_Exception
     * @return string
     */
    protected function _getMethodByExtension($extension)
    {
        foreach($this->_allowedExtensions as $allowedExtension) {
            if ($allowedExtension == $extension) {
                return 'getDataFrom' . ucfirst($allowedExtension);
            }
        }

        throw new Magento_Core_Exception($this->_getFileTypeMessageText());
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

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
namespace Magento\AdvancedCheckout\Model;

class Import extends \Magento\Object
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
     * Checkout data
     *
     * @var \Magento\AdvancedCheckout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\AdvancedCheckout\Helper\Data $checkoutData
     * @param array $data
     */
    public function __construct(
        \Magento\AdvancedCheckout\Helper\Data $checkoutData,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($data);
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
     * @return void
     */
    public function uploadFile()
    {
        /** @var $uploader \Magento\Core\Model\File\Uploader */
        $uploader  = \Mage::getModel('Magento\Core\Model\File\Uploader', array('fileId' => self::FIELD_NAME_SOURCE_FILE));
        $uploader->setAllowedExtensions($this->_allowedExtensions);
        $uploader->skipDbProcessing(true);
        if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
            \Mage::throwException($this->_getFileTypeMessageText());
        }

        try {
            $result = $uploader->save($this->_getWorkingDir());
            $this->_uploadedFile = $result['path'] . $result['file'];
        } catch (\Exception $e) {
            \Mage::throwException($this->_checkoutData->getFileGeneralErrorText());
        }
    }

    /**
     * Get rows from file
     *
     * @return array
     */
    public function getRows()
    {
        $extension = pathinfo($this->_uploadedFile, PATHINFO_EXTENSION);
        $method = $this->_getMethodByExtension(strtolower($extension));
        if (!empty($method) && method_exists($this, $method)) {
            return $this->$method();
        }

        \Mage::throwException($this->_getFileTypeMessageText());
    }

    /**
     * Get rows from CSV file
     *
     * @return array
     */
    public function getDataFromCsv()
    {
        if (!$this->_uploadedFile || !file_exists($this->_uploadedFile)) {
            \Mage::throwException($this->_checkoutData->getFileGeneralErrorText());
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
                        \Mage::throwException($this->_checkoutData->getSkuEmptyDataMessageText());
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
        } catch (\Exception $e) {
            \Mage::throwException(__('The file is corrupt.'));
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
        return \Mage::getBaseDir('var') . DS . 'import_sku' . DS;
    }

    /**
     * Get Method to load data by file extension
     *
     * @param string $extension
     * @return bool|string
     */
    protected function _getMethodByExtension($extension)
    {
        foreach($this->_allowedExtensions as $allowedExtension) {
            if ($allowedExtension == $extension) {
                return 'getDataFrom' . ucfirst($allowedExtension);
            }
        }

        \Mage::throwException($this->_getFileTypeMessageText());
        return false;
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

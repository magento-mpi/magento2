<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import data from file
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Model_Import extends Magento_Object
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
        /** @var $uploader Magento_Core_Model_File_Uploader */
        $uploader  = Mage::getModel('Magento_Core_Model_File_Uploader', array('fileId' => self::FIELD_NAME_SOURCE_FILE));
        $uploader->setAllowedExtensions($this->_allowedExtensions);
        $uploader->skipDbProcessing(true);
        if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
            Mage::throwException($this->_getFileTypeMessageText());
        }

        try {
            $result = $uploader->save($this->_getWorkingDir());
            $this->_uploadedFile = $result['path'] . $result['file'];
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->getFileGeneralErrorText());
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

        Mage::throwException($this->_getFileTypeMessageText());
    }

    /**
     * Get rows from CSV file
     *
     * @return array
     */
    public function getDataFromCsv()
    {
        if (!$this->_uploadedFile || !file_exists($this->_uploadedFile)) {
            Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->getFileGeneralErrorText());
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
                        Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->getSkuEmptyDataMessageText());
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
            Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('The file is corrupt.'));
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
        return Mage::getBaseDir('var') . DS . 'import_sku' . DS;
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

        Mage::throwException($this->_getFileTypeMessageText());
        return false;
    }

    /**
     * Get message text of wrong file type error
     *
     * @return string
     */
    protected function _getFileTypeMessageText()
    {
        return Mage::helper('Enterprise_Checkout_Helper_Data')->__('This file needs to be in .csv format.');
    }
}

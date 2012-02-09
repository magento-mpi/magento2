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
class Enterprise_Checkout_Model_Import extends Varien_Object
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
     * @return bool
     */
    public function uploadFile()
    {
        $result = true;

        try {
            /** @var $uploader Mage_Core_Model_File_Uploader */
            $uploader  = Mage::getModel('Mage_Core_Model_File_Uploader', self::FIELD_NAME_SOURCE_FILE);
        } catch (Exception $e) {
            $result = false;
        }

        if ($result) {
            try {
                $uploader->setAllowedExtensions($this->_allowedExtensions);
                $uploader->skipDbProcessing(true);
                if (!$uploader->checkAllowedExtension($uploader->getFileExtension())) {
                    Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Only .csv file format is supported.'));
                }
                $result = $uploader->save($this->_getWorkingDir());
            } catch (Mage_Core_Exception $e) {
                Mage::throwException($e->getMessage());
            } catch (Exception $e) {
                Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Error while uploading file.'));
            }
        }

        if ($result !== false && !empty($result['file'])) {
            $this->_uploadedFile = $result['path'] . $result['file'];
        } else {
            return $result;
        }

        return true;
    }

    /**
     * Get rows from file
     *
     * @return array|bool
     */
    public function getRows()
    {
        $extension = pathinfo($this->_uploadedFile, PATHINFO_EXTENSION);
        $method = $this->_getMethodByExtension(strtolower($extension));
        if (!empty($method) && method_exists($this, $method)) {
            return $this->$method();
        }

        Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Not supported file type.'));
        return false;
    }

    /**
     * Get rows from CSV file
     *
     * @return array
     */
    public function getDataFromCsv()
    {
        if (!$this->_uploadedFile || !file_exists($this->_uploadedFile)) {
            Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Uploaded file not exists'));
        }

        $csvData = array();
        $currentKey = 0;

        try {
            $fileHandler = fopen($this->_uploadedFile, 'r');
            if ($fileHandler) {
                rewind($fileHandler);
                $colNames = fgetcsv($fileHandler);
                $num = count($colNames);
                if ($num != 2) {
                    Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Uploaded file is invalid'));
                }
                for ($i = 0; $i < 2; $i++) {
                    // If header columns specified as "sku, qty" - it could cause problems because of the whitespace
                    $colNames[$i] = trim($colNames[$i]);
                }
                while (($currentRow = fgetcsv($fileHandler)) !== false) {
                    $num = count($currentRow);
                    if ($num != 2) {
                        continue;
                    }
                    $csvDataRow = array();
                    for ($i = 0; $i < 2; $i++) {
                        $csvDataRow[$colNames[$i]] = trim($currentRow[$i]);
                    }
                    $csvData[] = $csvDataRow;
                    $currentKey++;
                }
                fclose($fileHandler);
            }
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('File is corrupt.'));
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

        Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Not supported file type.'));
        return false;
    }

    /**
     * Whether a file has been submitted by user
     *
     * @return bool
     */
    public function hasAnythingToUpload()
    {
        return !empty($_FILES);
    }
}

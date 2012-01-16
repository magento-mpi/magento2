<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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

                $result = $uploader->save($this->_getWorkingDir());
            } catch (Exception $e) {
                Mage::throwException(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Error in uploading file.'));
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
}

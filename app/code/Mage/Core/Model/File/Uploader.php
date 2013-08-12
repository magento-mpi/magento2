<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core file uploader model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Uploader extends Magento_File_Uploader
{
    /**
     * Flag, that defines should DB processing be skipped
     *
     * @var bool
     */
    protected $_skipDbProcessing = false;

    /**
     * Save file to storage
     *
     * @param  array $result
     * @return Mage_Core_Model_File_Uploader
     */
    protected function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }

        /** @var $helper Mage_Core_Helper_File_Storage */
        $helper = Mage::helper('Mage_Core_Helper_File_Storage');

        if ($helper->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        /** @var $dbHelper Mage_Core_Helper_File_Storage_Database */
        $dbHelper = Mage::helper('Mage_Core_Helper_File_Storage_Database');
        $this->_result['file'] = $dbHelper->saveUploadedFile($result);

        return $this;
    }

    /**
     * Getter/Setter for _skipDbProcessing flag
     *
     * @param null|bool $flag
     * @return bool|Mage_Core_Model_File_Uploader
     */
    public function skipDbProcessing($flag = null)
    {
        if (is_null($flag)) {
            return $this->_skipDbProcessing;
        }
        $this->_skipDbProcessing = (bool)$flag;
        return $this;
    }

    /**
     * Check protected/allowed extension
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        //validate with protected file types
        /** @var $validator Mage_Core_Model_File_Validator_NotProtectedExtension */
        $validator = Mage::getSingleton('Mage_Core_Model_File_Validator_NotProtectedExtension');
        if (!$validator->isValid($extension)) {
            return false;
        }

        return parent::checkAllowedExtension($extension);
    }

    /**
     * Get file size
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->_file['size'];
    }

    /**
     * Validate file
     *
     * @return array
     */
    public function validateFile()
    {
        $this->_validateFile();
        return $this->_file;
    }
}

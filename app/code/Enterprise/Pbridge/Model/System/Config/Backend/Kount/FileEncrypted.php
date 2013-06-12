<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

include_once 'easyarchive/EasyArchive.class.php';

/**
 * Credentials Archive model
 *
 * @category   Enterprise
 * @package    Enterprise_Pbridge
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_System_Config_Backend_Kount_FileEncrypted extends Mage_Core_Model_Config_Data
{
    /**
     * Decrypt value after loading
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if (!empty($value) && ($decrypted = Mage::helper('Mage_Core_Helper_Data')->decrypt($value))) {
            $this->setValue($decrypted);
        }
    }

    /**
     * Open uploaded archive and parse files for saving config values
     *
     * @return Enterprise_Pbridge_Model_System_Config_Backend_Kount_FileEncrypted
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            return $this;
        } else {
            $this->setValue($this->getOldValue());
        }

        if ($_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value']){

            if (!$this->_checkExtension(
                $_FILES['groups']['name'][$this->getGroupId()]['fields'][$this->getField()]['value']
            )) {
                Mage::throwException(
                    Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Certificate file uploaded wrong file')
                );
            }

            $data = array();
            try {
                $tmpFile = $_FILES['groups']['tmp_name'][$this->getGroupId()]['fields'][$this->getField()]['value'];
                $data = file_get_contents($tmpFile);
                $data = base64_encode($data);
            } catch (Exception $e) {
                Mage::throwException($e->getMessage());
            }

            if ($data) {
                $this->setValue($data);
            }
        }

        return $this;
    }

    /**
     * Check extension uploaded file
     *
     * @param string $fileName
     * @return bool
     */
    protected function _checkExtension($fileName)
    {
        return fnmatch('*.p12', $fileName) || fnmatch('*.pfx', $fileName);
    }
}

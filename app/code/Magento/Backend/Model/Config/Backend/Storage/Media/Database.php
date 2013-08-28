<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Backend_Storage_Media_Database extends Magento_Core_Model_Config_Data
{
    /**
     * Create db structure
     *
     * @return Magento_Backend_Model_Config_Backend_Storage_Media_Database
     */
    protected function _afterSave()
    {
        $helper = Mage::helper('Magento_Core_Helper_File_Storage');
        $helper->getStorageModel(null, array('init' => true));

        return $this;
    }
}

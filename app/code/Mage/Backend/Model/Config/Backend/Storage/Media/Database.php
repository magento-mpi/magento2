<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Backend_Storage_Media_Database extends Magento_Core_Model_Config_Data
{
    /**
     * Create db structure
     *
     * @return Mage_Backend_Model_Config_Backend_Storage_Media_Database
     */
    protected function _afterSave()
    {
        $helper = Mage::helper('Magento_Core_Helper_File_Storage');
        $helper->getStorageModel(null, array('init' => true));

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Backend_Storage_Media_Database extends Mage_Core_Model_Config_Data
{
    /**
     * Create db structure
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Storage_Media_Database
     */
    protected function _afterSave()
    {
        $helper = Mage::helper('Mage_Core_Helper_File_Storage');
        $helper->getStorageModel(null, array('init' => true));

        return $this;
    }
}

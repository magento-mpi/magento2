<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Backend_Secure extends Mage_Core_Model_Config_Data
{
    /**
     * Clean compiled JS/CSS when updating configuration settings
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('Mage_Core_Model_Design_PackageInterface')->cleanMergedJsCss();
        }
    }
}

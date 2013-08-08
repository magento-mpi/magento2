<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Backend_Theme extends Magento_Core_Model_Config_Data
{
    /**
     * Validate specified value against frontend area
     */
    protected function _beforeSave()
    {
        $design = clone Mage::getDesign();
        $design->setDesignTheme($this->getValue(), Magento_Core_Model_App_Area::AREA_FRONTEND);
        return parent::_beforeSave();
    }
}

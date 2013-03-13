<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Backend_Theme extends Mage_Core_Model_Config_Data
{
    /**
     * Validate specified value against frontend area
     */
    protected function _beforeSave()
    {
        $design = clone Mage::getDesign();
        $design->setDesignTheme($this->getValue(), Mage_Core_Model_App_Area::AREA_FRONTEND);
        return parent::_beforeSave();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sitemap_Model_Config_Backend_Priority extends Mage_Core_Model_Config_Value
{

    protected function _beforeSave()
    {
        $value     = $this->getValue();
            if ($value < 0 || $value > 1) {
                throw new Exception(Mage::helper('Mage_Sitemap_Helper_Data')->__('The priority must be between 0 and 1.'));
            } elseif (($value == 0) && !($value === '0' || $value === '0.0')) {
                throw new Exception(Mage::helper('Mage_Sitemap_Helper_Data')->__('The priority must be between 0 and 1.'));
            }
        return $this;
    }

}

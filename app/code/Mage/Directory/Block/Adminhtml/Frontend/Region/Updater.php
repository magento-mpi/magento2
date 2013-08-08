<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Directory_Block_Adminhtml_Frontend_Region_Updater
    extends Mage_Backend_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= "<script type=\"text/javascript\">var updater = new RegionUpdater('tax_defaults_country',"
            . " 'tax_region', 'tax_defaults_region', "
            . $this->helper('Mage_Directory_Helper_Data')->getRegionJson()
            . ", 'disable');</script>";

        return $html;
    }
}




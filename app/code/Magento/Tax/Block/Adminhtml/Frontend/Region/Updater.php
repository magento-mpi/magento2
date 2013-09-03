<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Block_Adminhtml_Frontend_Region_Updater
    extends Magento_Backend_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);

        $js = '<script type="text/javascript">
               var updater = new RegionUpdater("tax_defaults_country", "none", "tax_defaults_region", %s, "nullify");
               if(updater.lastCountryId) {
                   var tmpRegionId = $("tax_defaults_region").value;
                   var tmpCountryId = updater.lastCountryId;
                   updater.lastCountryId=false;
                   updater.update();
                   updater.lastCountryId = tmpCountryId;
                   $("tax_defaults_region").value = tmpRegionId;
               } else {
                   updater.update();
               }
               </script>';

        $html .= sprintf($js, $this->helper('Magento_Directory_Helper_Data')->getRegionJson());
        return $html;
    }
}




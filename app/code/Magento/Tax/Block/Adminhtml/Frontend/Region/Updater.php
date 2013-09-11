<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Block\Adminhtml\Frontend\Region;

class Updater
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
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

        $html .= sprintf($js, $this->helper('\Magento\Directory\Helper\Data')->getRegionJson());
        return $html;
    }
}




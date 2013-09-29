<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Directory\Block\Adminhtml\Frontend\Region;

class Updater
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= "<script type=\"text/javascript\">var updater = new RegionUpdater('tax_defaults_country',"
            . " 'tax_region', 'tax_defaults_region', "
            . $this->helper('Magento\Directory\Helper\Data')->getRegionJson()
            . ", 'disable');</script>";

        return $html;
    }
}




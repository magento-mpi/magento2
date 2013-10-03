<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Block\Adminhtml\Shipping\Applicable;

class Countries
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();

    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::_getElementHtml($element);
        $html .= $this->_appendJs($element);
        return $html;
    }

    protected function _appendJs($element)
    {
        $elId = $element->getHtmlId();
        $childId = str_replace('sallowspecific', 'specificcountry', $elId);
        $html = "<script type='text/javascript'>
        var dwvie = function ()
        {
            var valueSelectId = '{$elId}';
            var elementToDisableId = '{$childId}';

            var source = $(valueSelectId);
            var target = $(elementToDisableId);

            if (source.options[source.selectedIndex].value == '0') {
                target.disabled = true;
            } else {
                target.disabled = false;
            }
        }

        Event.observe('{$elId}', 'change', dwvie);
        Event.observe(window, 'load', dwvie);
        </script>";
        return $html;
    }
}

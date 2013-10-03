<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML select element block
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Block\Html;

class Date extends \Magento\Core\Block\Template
{
    protected function _toHtml()
    {
        $html  = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';
        $calendarYearsRange = $this->getYearsRange();
        $html .=
            '<script type="text/javascript">
            //<![CDATA[
            (function($) {
                $(document).ready(function(){
                    $("#' . $this->getId() . '").calendar({
                        showsTime: ' . ($this->getTimeFormat() ? 'true' : 'false') . ',
                        ' . ($this->getTimeFormat() ? ('timeFormat: "' . $this->getTimeFormat() . '",') : '') . '
                        dateFormat: "' . $this->getDateFormat() . '",
                        buttonImage: "' . $this->getImage() . '",
                        ' . ($calendarYearsRange ? 'yearRange: "' . $calendarYearsRange . '",' : '') . '
                        buttonText: "' . __('Select Date') . '"
                    })
                });
            })(jQuery)
            //]]>
            </script>';

        return $html;
    }

    public function getEscapedValue($index=null) {

        if($this->getFormat() && $this->getValue()) {
            return strftime($this->getFormat(), strtotime($this->getValue()));
        }

        return htmlspecialchars($this->getValue());
    }

    public function getHtml()
    {
        return $this->toHtml();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * HTML select element block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Html_Date extends Mage_Core_Block_Template
{

    protected function _toHtml()
    {
        $displayFormat = Varien_Date::convertZendToStrFtime($this->getFormat(), true, (bool)$this->getTime());

        $html  = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';

        /*$html .= '<img src="' . $this->getImage() . '" alt="' . $this->helper('Mage_Core_Helper_Data')->__('Select Date') . '" class="v-middle" ';
        $html .= 'title="' . $this->helper('Mage_Core_Helper_Data')->__('Select Date') . '" id="' . $this->getId() . '_trig" />';

        $html .=
        '<script type="text/javascript">
        //<![CDATA[
            var calendarSetupObject = {
                inputField  : "' . $this->getId() . '",
                ifFormat    : "' . $displayFormat . '",
                showsTime   : "' . ($this->getTime() ? 'true' : 'false') . '",
                button      : "' . $this->getId() . '_trig",
                align       : "Bl",
                singleClick : true
            }';

        $calendarYearsRange = $this->getYearsRange();
        if ($calendarYearsRange) {
            $html .= '
                calendarSetupObject.range = ' . $calendarYearsRange . '
                ';
        }

        $html .= '
            Calendar.setup(calendarSetupObject);
        //]]>
        </script>'*/;
        $calendarYearsRange = $this->getYearsRange();
        $html .=
            '<script type="text/javascript">
            //<![CDATA[
                (function( $ ) {
                    $("#' . $this->getId() . '").calendar({
                        buttonImage: "' . $this->getImage() . '",
                        buttonText: "' . $this->helper('Mage_Core_Helper_Data')->__('Select Date') . '",
                        '. ($calendarYearsRange ? 'yearRange: ' . $calendarYearsRange . '' : '') . '
                    })
                })(jQuery)';

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

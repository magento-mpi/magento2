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
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Html_Date_Jquery extends Mage_Core_Block_Html_Date
{

    protected function _toHtml()
    {
        $displayFormat = Varien_Jquery_Date::convertZendToStrFtime($this->getFormat(), true, (bool)$this->getTime());

        $html = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" class="' . $this->getClass() . '" '
            . $this->getExtraParams() . '/> ';

        $yearRange = "c-10:c+10"; /* Default the range to the current year + or - 10 years. */
        $calendarYearsRange = $this->getYearsRange();
        if ($calendarYearsRange) {
            /* Convert to the year range format that the jQuery datepicker understands. */
            sscanf($calendarYearsRange, "[%[0-9], %[0-9]]", $yearStart, $yearEnd);
            $yearRange = "$yearStart:$yearEnd";
        }

        $regionalJsFile
            = '/pub/lib/jquery/ui/i18n/jquery.ui.datepicker-' . /* The datepicker localized settings file. */
                substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2) . '.js';

        $jsFiles = '"/pub/lib/jquery/ui/jquery-ui.js", '; /* First include jquery-ui. */
        if (file_exists(BP . $regionalJsFile)) {
            $jsFiles .= '"' . $regionalJsFile . '", '; /* Followed by the regional file, if it exists. */
        }
        $jsFiles .= '"/pub/lib/mage/calendar/calendar.js"'; /* Lastly, the datepicker. */

        $html .= '
            <script type="text/javascript">
                //<![CDATA[
                mage.event.observe("mage.calendar.initialize", function (event, initData) {
                    var datepicker = {
                        inputSelector: "#' . $this->getId() . '",
                        options: {
                            buttonImage: "' . $this->getImage() . '",
                            buttonText: "' . $this->helper("Mage_Core_Helper_Data")->__("Select Date") . '",
                            dateFormat: "' . $displayFormat . '",
                            yearRange: "' . $yearRange . '"
                        }
                    };
                    initData.datepicker.push(datepicker);
                });
                mage.load.css("/pub/lib/mage/calendar/css/calendar.css");
                mage.load.jsSync(' . $jsFiles . ');
                //]]>
            </script>';

        return $html;
    }
}

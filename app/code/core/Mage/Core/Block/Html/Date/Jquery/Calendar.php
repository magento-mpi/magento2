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
 * HTML calendar element block implemented using the jQuery datepicker widget.
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Html_Date_Jquery_Calendar extends Mage_Core_Block_Html_Date
{

    /**
     * File path and filename prefix for the regional localized Javascript file.
     */
    const LOCALIZED_FILE_PREFIX = '/pub/lib/jquery/ui/i18n/jquery.ui.datepicker-';

    /**
     * Return the locale code based on the existence of a localized Javascript file. Can be
     * either a five character code (e.g. en-US) or a two character code (e.g. en).
     *
     * @return string
     */
    private function _getLocaleForRegionalJsFile()
    {
        $locale = str_replace('_', '-', Mage::app()->getLocale()->getLocaleCode());

        /* First check for the 5 character localized file. There are a small handful. */
        if (file_exists(BP . self::LOCALIZED_FILE_PREFIX . $locale . '.js')) {
            return $locale;
        } else {
            /* Most of the localized files use a two character locale code. */
            $locale = substr($locale, 0, 2);
            if (file_exists(BP . self::LOCALIZED_FILE_PREFIX . $locale . '.js')) {
                return $locale;
            }
        }

        return ''; /* Default to an empty string. This will default the jQuery datepicker to English. */
    }

    /**
     * Generate HTML containing a Javascript <script> tag for creating a calendar instance implemented
     * using the jQuery datepicker.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $displayFormat = Magento_Date_Jquery_Calendar::convertToDateTimeFormat(
            $this->getFormat(), true, (bool)$this->getTime()
        );

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

        $jsFiles = '"/pub/lib/jquery/ui/jquery-ui.js", '; /* First include jquery-ui. */

        $locale = $this->_getLocaleForRegionalJsFile();
        if (strlen($locale) > 0) {
            /* Followed by the regional localized file, if it exists. */
            $jsFiles .= '"' . self::LOCALIZED_FILE_PREFIX . $locale . '.js", ';
        }

        $jsFiles .= '"/pub/lib/mage/calendar/calendar.js"'; /* Lastly, the datepicker. */

        $html
            .= '
            <script type="text/javascript">
                //<![CDATA[
                mage.event.observe("mage.calendar.initialize", function (event, initData) {
                    var datepicker = {
                        inputSelector: "#' . $this->getId() . '",
                        locale: "' . $locale . '",
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

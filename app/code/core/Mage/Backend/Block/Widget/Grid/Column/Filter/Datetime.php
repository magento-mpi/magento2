<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Date grid column filter
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo        date format
 */
class Mage_Backend_Block_Widget_Grid_Column_Filter_Datetime extends Mage_Backend_Block_Widget_Grid_Column_Filter_Date
{
    //full day is 86400, we need 23 hours:59 minutes:59 seconds = 86399
    const END_OF_DAY_IN_SECONDS = 86399;

    public function getValue($index=null)
    {
        if ($index) {
            if ($data = $this->getData('value', 'orig_'.$index)) {
                return $data;//date('Y-m-d', strtotime($data));
            }
            return null;
        }
        $value = $this->getData('value');
        if (is_array($value)) {
            $value['datetime'] = true;
        }
        if (!empty($value['to']) && !$this->getColumn()->getFilterTime()) {
            $datetimeTo = $value['to'];

            //calculate end date considering timezone specification
            $datetimeTo->setTimezone(
                Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)
            );
            $datetimeTo->addDay(1)->subSecond(1);
            $datetimeTo->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        }
        return $value;
    }

    /*
     * Convert given date to default (UTC) timezone
     *
     * @param string $date
     * @param string $locale
     * @return Zend_Date
     */
    protected function _convertDate($date, $locale)
    {
        if ($this->getColumn()->getFilterTime()) {
            try {
                $dateObj = $this->getLocale()->date(null, null, $locale, false);

                //set default timezone for store (admin)
                $dateObj->setTimezone(
                    Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE)
                );

                //set date with applying timezone of store
                $dateObj->set(
                    $date,
                    $this->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    $locale
                );

                //convert store date to default date in UTC timezone without DST
                $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

                return $dateObj;
            }
            catch (Exception $e) {
                return null;
            }
        }

        return parent::_convertDate($date, $locale);
    }

    /**
     * Render filter html
     *
     * @return string
     */
    public function getHtml()
    {
        $htmlId = Mage::helper('Mage_Core_Helper_Data')->uniqHash($this->_getHtmlId());
        $format = $this->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $timeFormat = '';

        if ($this->getColumn()->getFilterTime()) {
            $timeFormat = $this->getLocale()->getTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        }

        $html = '<div class="range" id="'.$htmlId.'_range"><div class="range-line date">'
            . '<span class="label">' . Mage::helper('Mage_Backend_Helper_Data')->__('From').':</span>'
            . '<input type="text" name="'.$this->_getHtmlName().'[from]" id="'.$htmlId.'_from"'
                . ' value="'.$this->getEscapedValue('from').'" class="input-text no-changes" ' . $this->getUiId('filter', $this->_getHtmlName(), 'from') .  '/>'
            . '</div>';
        $html.= '<div class="range-line date">'
            . '<span class="label">' . Mage::helper('Mage_Backend_Helper_Data')->__('To').' :</span>'
            . '<input type="text" name="'.$this->_getHtmlName().'[to]" id="'.$htmlId.'_to"'
                . ' value="'.$this->getEscapedValue('to').'" class="input-text no-changes" ' . $this->getUiId('filter', $this->_getHtmlName(), 'to') .  '/>'
            . '</div></div>';
        $html.= '<input type="hidden" name="'.$this->_getHtmlName().'[locale]"'
            . ' value="'.$this->getLocale()->getLocaleCode().'"/>';
        $html.= '<script type="text/javascript">
            (function( $ ) {
                    $("#'.$htmlId.'_range").dateRange({
                        dateFormat: "' . $format . '",
                        timeFormat: "' . $timeFormat . '",
                        showsTime: '. ( $this->getColumn()->getFilterTime() ? 'true' : 'false') .',
                        buttonImage: "'. Mage::getDesign()->getSkinUrl('images/grid-cal.gif') . '",
                        buttonText: "'.$this->escapeHtml(Mage::helper('Mage_Backend_Helper_Data')->__('Date selector')).'",
                        from: {
                            id: "'.$htmlId.'_from"
                        },
                        to: {
                            id: "'.$htmlId.'_to"
                        }
                    })
            })(jQuery)
        </script>';
        return $html;
    }

    /**
     * Return escaped value for calendar
     *
     * @param string $index
     * @return string
     */
    public function getEscapedValue($index=null)
    {
        if ($this->getColumn()->getFilterTime()) {
            $value = $this->getValue($index);
            if ($value instanceof Zend_Date) {
                return $value->toString(
                    $this->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                );
            }
            return $value;
        }

        return parent::getEscapedValue($index);
    }

}

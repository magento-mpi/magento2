<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for Date
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomAttribute_Block_Form_Renderer_Date extends Magento_CustomAttribute_Block_Form_Renderer_Abstract
{
    /**
     * Constants for borders of date-type customer attributes
     */
    const MIN_DATE_RANGE_KEY = 'date_range_min';
    const MAX_DATE_RANGE_KEY = 'date_range_max';

    /**
     * Array of date parts html fragments keyed by date part code
     *
     * @var array
     */
    protected $_dateInputs  = array();

    /**
     * Array of minimal and maximal date range values
     *
     * @var array|null
     */
    protected $_dateRange = null;

    /**
     * Returns format which will be applied for date field in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
    }

    /**
     * Add date input html
     *
     * @param string $code
     * @param string $html
     */
    public function setDateInput($code, $html)
    {
        $this->_dateInputs[$code] = $html;
    }

    /**
     * Sort date inputs by dateformat order of current locale
     *
     * @return string
     */
    public function getSortedDateInputs()
    {
        $strtr = array(
            '%b' => '%1$s',
            '%B' => '%1$s',
            '%m' => '%1$s',
            '%d' => '%2$s',
            '%e' => '%2$s',
            '%Y' => '%3$s',
            '%y' => '%3$s'
        );

        $dateFormat = preg_replace('/[^\%\w]/', '\\1', $this->getDateFormat());

        return sprintf(strtr($dateFormat, $strtr),
            $this->_dateInputs['m'], $this->_dateInputs['d'], $this->_dateInputs['y']);
    }

    /**
     * Return value as unix time stamp or false
     *
     * @return int|false
     */
    public function getTimestamp()
    {
        $timestamp         = $this->getData('timestamp');
        $attributeCodeThis = $this->getData('attribute_code');
        $attributeCodeObj  = $this->getAttributeObject()->getAttributeCode();
        if (is_null($timestamp) || $attributeCodeThis != $attributeCodeObj) {
            $value = $this->getValue();
            if ($value) {
                if (is_numeric($value)) {
                    $timestamp = $value;
                } else {
                    $timestamp = strtotime($value);
                }
            } else {
                $timestamp = false;
            }
            $this->setData('timestamp', $timestamp);
            $this->setData('attribute_code', $attributeCodeObj);
        }
        return $timestamp;
    }

    /**
     * Return Date part by index
     *
     * @param string $index allowed index (Y,m,d)
     * @return string
     */
    protected function _getDateValue($index)
    {
        if ($this->getTimestamp()) {
            return date($index, $this->getTimestamp());
        }
        return '';
    }

    /**
     * Return day value from date
     *
     * @return string
     */
    public function getDay()
    {
        return $this->_getDateValue('d');
    }

    /**
     * Return month value from date
     *
     * @return string
     */
    public function getMonth()
    {
        return $this->_getDateValue('m');
    }

    /**
     * Return year value from date
     *
     * @return string
     */
    public function getYear()
    {
        return $this->_getDateValue('Y');
    }

    /**
     * Return minimal date range value
     *
     * @return string
     */
    public function getMinDateRange()
    {
        return $this->_getBorderDateRange(self::MIN_DATE_RANGE_KEY);
    }

    /**
     * Return maximal date range value
     *
     * @return string
     */
    public function getMaxDateRange()
    {
        return $this->_getBorderDateRange(self::MAX_DATE_RANGE_KEY);
    }

    /**
     * Return minimal or maximal date range value
     *
     * Result is to be inserted to JS, so it can be 'false'
     *
     * @return string
     */
    protected function _getBorderDateRange($borderName = self::MIN_DATE_RANGE_KEY)
    {
        $dateRange = $this->_getDateRange();
        if (isset($dateRange[$borderName])) {
            return $dateRange[$borderName] * 1000; //miliseconds for JS
        } else {
            return 'false';
        }
    }

    /**
     * Return array of date range border values
     *
     * @return array
     */
    protected function _getDateRange()
    {
        if (is_null($this->_dateRange)) {
            $this->_dateRange = array();
            $rules = $this->getAttributeObject()->getValidateRules();
            if (isset($rules[self::MIN_DATE_RANGE_KEY])) {
                $this->_dateRange[self::MIN_DATE_RANGE_KEY] = $rules[self::MIN_DATE_RANGE_KEY];
            }
            if (isset($rules[self::MAX_DATE_RANGE_KEY])) {
                $this->_dateRange[self::MAX_DATE_RANGE_KEY] = $rules[self::MAX_DATE_RANGE_KEY];
            }
        }
        return $this->_dateRange;
    }
}

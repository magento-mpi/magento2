<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Widget;

class Dob extends \Magento\Customer\Block\Widget\AbstractWidget
{
    /**
     * Date inputs
     *
     * @var array
     */
    protected $_dateInputs = array();

    /**
     * Constants for borders of date-type customer attributes
     */
    const MIN_DATE_RANGE_KEY = 'date_range_min';
    const MAX_DATE_RANGE_KEY = 'date_range_max';

    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('widget/dob.phtml');
    }

    public function isEnabled()
    {
        return (bool)$this->_getAttribute('dob')->getIsVisible();
    }

    public function isRequired()
    {
        return (bool)$this->_getAttribute('dob')->getIsRequired();
    }

    public function setDate($date)
    {
        $this->setTime($date ? strtotime($date) : false);
        $this->setData('date', $date);
        return $this;
    }

    public function getDay()
    {
        return $this->getTime() ? date('d', $this->getTime()) : '';
    }

    public function getMonth()
    {
        return $this->getTime() ? date('m', $this->getTime()) : '';
    }

    public function getYear()
    {
        return $this->getTime() ? date('Y', $this->getTime()) : '';
    }

    /**
     * Returns format which will be applied for DOB in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
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
        $mapping = array(
            '/[^medy]/i' => '\\1',
            '/m{1,5}/i' => '%1$s',
            '/e{1,5}/i' => '%2$s',
            '/d{1,5}/i' => '%2$s',
            '/y{1,5}/i' => '%3$s',
        );

        $dateFormat = preg_replace(
            array_keys($mapping),
            array_values($mapping),
            $this->getDateFormat()
        );

        return sprintf($dateFormat,
            $this->_dateInputs['m'], $this->_dateInputs['d'], $this->_dateInputs['y']);
    }

    /**
     * Return minimal date range value
     *
     * @return string
     */
    public function getMinDateRange()
    {
        $rules = $this->_getAttribute('dob')->getValidateRules();
        return isset($rules[self::MIN_DATE_RANGE_KEY]) ? date("Y/m/d", $rules[self::MIN_DATE_RANGE_KEY]) : null;
    }

    /**
     * Return maximal date range value
     *
     * @return string
     */
    public function getMaxDateRange()
    {
        $rules = $this->_getAttribute('dob')->getValidateRules();
        return isset($rules[self::MAX_DATE_RANGE_KEY]) ? date("Y/m/d", $rules[self::MAX_DATE_RANGE_KEY]) : null;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Form Input/Output Strip HTML tags Filter
 *
 * @category    Magento
 * @package     Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Filter;

class Date implements \Magento\Data\Form\Filter\FilterInterface
{
    /**
     * Date format
     *
     * @var string
     */
    protected $_dateFormat;

    /**
     * Local
     *
     * @var \Zend_Locale
     */
    protected $_locale;

    /**
     * Initialize filter
     *
     * @param string $format    \Zend_Date input/output format
     * @param \Zend_Locale $locale
     */
    public function __construct($format = null, $locale = null)
    {
        if (is_null($format)) {
            $format = \Magento\Date::DATE_INTERNAL_FORMAT;
        }
        $this->_dateFormat  = $format;
        $this->_locale      = $locale;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value)
    {
        $filterInput = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format'   => $this->_dateFormat,
            'locale'        => $this->_locale
        ));
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format'   => \Magento\Date::DATE_INTERNAL_FORMAT,
            'locale'        => $this->_locale
        ));

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);
        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value)
    {
        $filterInput = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format'   => \Magento\Date::DATE_INTERNAL_FORMAT,
            'locale'        => $this->_locale
        ));
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format'   => $this->_dateFormat,
            'locale'        => $this->_locale
        ));

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);
        return $value;
    }
}

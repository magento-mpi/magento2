<?php
/**
 * Date filter. Converts date from localized to internal format.
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
/**
 * @todo move this class to library when locale interface is moved
 */
namespace Magento\Core\Filter;

use Magento\LocaleInterface;

class Date implements \Zend_Filter_Interface
{
    /**
     * Filter that converts localized input into normalized format
     *
     * @var \Zend_Filter_LocalizedToNormalized
     */
    protected $_localToNormalFilter;

    /**
     * Filter that converts normalized input into internal format
     *
     * @var \Zend_Filter_NormalizedToLocalized
     */
    protected $_normalToLocalFilter;

    /**
     * @param \Magento\LocaleInterface $locale
     */
    public function __construct(
        LocaleInterface $locale
    ) {
        $this->_localToNormalFilter = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $locale->getDateFormat(LocaleInterface::FORMAT_TYPE_SHORT)
        ));
        $this->_normalToLocalFilter = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Stdlib\DateTime::DATE_INTERNAL_FORMAT
        ));
    }

    /**
     * Convert date from localized to internal format
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return $this->_normalToLocalFilter->filter($this->_localToNormalFilter->filter($value));
    }
}

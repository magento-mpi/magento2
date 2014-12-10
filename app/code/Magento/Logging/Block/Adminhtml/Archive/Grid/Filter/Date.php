<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Custom date column filter for logging archive grid
 */
namespace Magento\Logging\Block\Adminhtml\Archive\Grid\Filter;

class Date extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Date
{
    /**
     * Convert date from localized to internal format
     *
     * @param string $date
     * @param string $locale
     * @return string
     */
    protected function _convertDate($date, $locale)
    {
        $filterInput = new \Zend_Filter_LocalizedToNormalized(
            [
                'date_format' => $this->_localeDate->getDateFormat(
                    \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_SHORT
                ),
            ]
        );
        $filterInternal = new \Zend_Filter_NormalizedToLocalized(
            ['date_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT]
        );
        $date = $filterInput->filter($date);
        $date = $filterInternal->filter($date);

        return $date;
    }
}

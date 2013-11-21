<?php
/**
 * Date/Time filter. Converts datetime from localized to internal format.
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
/**
 * @todo move this class to library when locale interface is moved
 */
namespace Magento\Core\Filter;

use Magento\Core\Model\LocaleInterface;

class DateTime extends Date
{
    /**
     * @param LocaleInterface $locale
     */
    public function __construct(
        LocaleInterface $locale
    ) {
        parent::__construct($locale);
        $this->_localToNormalFilter = new \Zend_Filter_LocalizedToNormalized(array(
            'date_format' => $locale->getDateTimeFormat(LocaleInterface::FORMAT_TYPE_SHORT)
        ));
        $this->_normalToLocalFilter = new \Zend_Filter_NormalizedToLocalized(array(
            'date_format' => \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT
        ));
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locale timezone source
 */
namespace Magento\Backend\Model\Config\Source\Locale;

class Weekdays implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\LocaleInterface $locale
     */
    public function __construct(\Magento\LocaleInterface $locale)
    {
        $this->_locale = $locale;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_locale->getOptionWeekdays();
    }
}

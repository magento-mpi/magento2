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
 * Locale weekdays source
 */
namespace Magento\Backend\Model\Config\Source\Locale;

class Weekdaycodes implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Locale\ListsInterface $localeLists)
    {
        $this->_localeLists = $localeLists;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_localeLists->getOptionWeekdays(true, true);
    }
}

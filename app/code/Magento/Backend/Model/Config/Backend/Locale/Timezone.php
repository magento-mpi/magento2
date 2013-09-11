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
 * System config email field backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Locale;

class Timezone extends \Magento\Core\Model\Config\Value
{
    /**
     * Const for PHP 5.3+ compatibility
     * This value copied from \DateTimeZone::ALL_WITH_BC in PHP 5.3+
     *
     * @constant ALL_WITH_BC
     */
    const ALL_WITH_BC = 4095;

    protected function _beforeSave()
    {
        $allWithBc = self::ALL_WITH_BC;
        if (defined('DateTimeZone::ALL_WITH_BC')) {
            $allWithBc = \DateTimeZone::ALL_WITH_BC;
        }

        if (!in_array($this->getValue(), \DateTimeZone::listIdentifiers($allWithBc))) {
            \Mage::throwException(__('Please correct the timezone.'));
        }

        return $this;
    }
}

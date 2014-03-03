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
 */
namespace Magento\Backend\Model\Config\Backend\Locale;

use Magento\Core\Exception;

class Timezone extends \Magento\Core\Model\Config\Value
{
    /**
     * @return $this
     * @throws Exception
     */
    protected function _beforeSave()
    {
        if (!in_array($this->getValue(), \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC))) {
            throw new Exception(__('Please correct the timezone.'));
        }
        return $this;
    }
}

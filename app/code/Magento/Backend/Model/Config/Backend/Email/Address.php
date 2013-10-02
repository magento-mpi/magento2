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
namespace Magento\Backend\Model\Config\Backend\Email;

class Address extends \Magento\Core\Model\Config\Value
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!\Zend_Validate::is($value, 'EmailAddress')) {
            throw new \Magento\Core\Exception(
                __('Please correct the email address: "%1".', $value)
            );
        }
        return $this;
    }
}

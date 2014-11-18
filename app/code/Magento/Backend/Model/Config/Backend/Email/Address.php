<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config email field backend model
 */
namespace Magento\Backend\Model\Config\Backend\Email;

use Magento\Framework\Model\Exception;

class Address extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     * @throws Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!\Zend_Validate::is($value, 'EmailAddress')) {
            throw new Exception(__('Please correct the email address: "%1".', $value));
        }
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Config\Backend\Password\Link;

/**
 * Customer Reset Password Link Expiration period backend model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Expirationperiod extends \Magento\App\Config\Value
{
    /**
     * Validate expiration period value before saving
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $resetPasswordLinkExpirationPeriod = (int)$this->getValue();

        if ($resetPasswordLinkExpirationPeriod < 1) {
            $resetPasswordLinkExpirationPeriod = (int)$this->getOldValue();
        }
        $this->setValue((string)$resetPasswordLinkExpirationPeriod);
        return $this;
    }
}

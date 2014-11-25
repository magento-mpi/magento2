<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Reset Password Link Expiration period backend model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin\Password\Link;

class Expirationperiod extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate expiration period value before saving
     *
     * @return $this
     */
    public function beforeSave()
    {
        parent::beforeSave();
        $expirationPeriod = (int)$this->getValue();

        if ($expirationPeriod < 1) {
            $expirationPeriod = (int)$this->getOldValue();
        }
        $this->setValue((string)$expirationPeriod);
        return $this;
    }
}

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
 * Admin Reset Password Link Expiration period backend model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend\Admin\Password\Link;

class Expirationperiod
    extends \Magento\Core\Model\Config\Value
{
    /**
     * Validate expiration period value before saving
     *
     * @return \Magento\Backend\Model\Config\Backend\Admin\Password\Link\Expirationperiod
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $expirationPeriod = (int)$this->getValue();

        if ($expirationPeriod < 1) {
            $expirationPeriod = (int)$this->getOldValue();
        }
        $this->setValue((string)$expirationPeriod);
        return $this;
    }
}

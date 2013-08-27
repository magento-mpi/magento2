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
class Magento_Backend_Model_Config_Backend_Admin_Password_Link_Expirationperiod
    extends Magento_Core_Model_Config_Data
{
    /**
     * Validate expiration period value before saving
     *
     * @return Magento_Backend_Model_Config_Backend_Admin_Password_Link_Expirationperiod
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

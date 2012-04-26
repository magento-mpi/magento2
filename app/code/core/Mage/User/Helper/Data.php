<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User data helper
 *
 * @category Mage
 * @package  Mage_User
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Configuration path to expiration period of reset password link
     */
    const XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
        = 'default/admin/emails/password_reset_link_expiration_period';

    /**
     * Retrieve customer reset password link expiration period in days
     *
     * @return int
     */
    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int) Mage::getConfig()->getNode(self::XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD);
    }
}

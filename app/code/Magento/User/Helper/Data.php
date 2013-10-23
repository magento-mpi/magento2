<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User data helper
 *
 * @category Magento
 * @package  Magento_User
 * @author   Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Configuration path to expiration period of reset password link
     */
    const XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
        = 'admin/emails/password_reset_link_expiration_period';

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $coreConfig
     * @internal param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
        parent::__construct($context);
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return \Magento\Math\Random::getUniqueHash();
    }

    /**
     * Retrieve customer reset password link expiration period in days
     *
     * @return int
     */
    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int)$this->_coreConfig->getValue(
            self::XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD,
            'default'
        );
    }
}

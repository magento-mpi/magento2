<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Helper;

/**
 * User data helper
 *
 * @category Magento
 * @package  Magento_User
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Configuration path to expiration period of reset password link
     */
    const XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
        = 'admin/emails/password_reset_link_expiration_period';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Math\Random $mathRandom
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Math\Random $mathRandom
    ) {
        $this->_config = $config;
        $this->mathRandom = $mathRandom;
        parent::__construct($context);
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return $this->mathRandom->getUniqueHash();
    }

    /**
     * Retrieve customer reset password link expiration period in days
     *
     * @return int
     */
    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int)$this->_config->getValue(self::XML_PATH_ADMIN_RESET_PASSWORD_LINK_EXPIRATION_PERIOD);
    }
}

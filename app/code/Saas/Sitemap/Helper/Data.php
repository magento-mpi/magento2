<?php
/**
 * Saas Sitemap Data helper
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Sitemap_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML path to Google Verification Code
     */
    const XML_PATH_GOOGLE_VERIFICATION_CODE = 'sitemap/generate/verification_code';

    /**
     * @var Mage_Core_Model_Store_ConfigInterface
     */
    protected $_config;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Store_ConfigInterface $config
     */
    public function __construct(Mage_Core_Helper_Context $context, Mage_Core_Model_Store_ConfigInterface $config)
    {
        parent::__construct($context);

        $this->_config = $config;
    }

    /**
     * Retrieve google verification code
     *
     * @return string
     */
    public function getGoogleVerificationCode()
    {
        return $this->_config->getConfig(self::XML_PATH_GOOGLE_VERIFICATION_CODE);
    }
}

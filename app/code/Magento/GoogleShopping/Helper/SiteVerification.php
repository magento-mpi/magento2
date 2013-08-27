<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Site Verification Helper
 */
class Magento_GoogleShopping_Helper_SiteVerification extends Magento_Core_Helper_Abstract
{
    /**
     * Name meta data for Google Site Verification
     */
    const META_NAME = 'google-site-verification';

    /**
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_GoogleShopping_Model_Config $config
     */
    public function __construct(Magento_Core_Helper_Context $context, Magento_GoogleShopping_Model_Config $config)
    {
        $this->_config = $config;
        parent::__construct($context);
    }

    /**
     * Get meta for site verification
     *
     * @param int $storeId
     * @return array
     */
    public function getMetaTag($storeId = null)
    {
        return array(
            'name'    => self::META_NAME,
            'content' => $this->_config->getConfigData('verify_meta_tag', $storeId)
        );
    }
}

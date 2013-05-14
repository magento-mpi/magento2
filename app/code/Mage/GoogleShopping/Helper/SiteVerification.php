<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Site Verification Helper
 */
class Mage_GoogleShopping_Helper_SiteVerification extends Mage_Core_Helper_Abstract
{
    /**
     * Name meta data for Google Site Verification
     */
    const META_NAME = 'google-site-verification';

    /**
     * @var Mage_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_GoogleShopping_Model_Config $config
     */
    public function __construct(Mage_Core_Helper_Context $context, Mage_GoogleShopping_Model_Config $config)
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

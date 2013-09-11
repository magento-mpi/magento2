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
namespace Magento\GoogleShopping\Helper;

class SiteVerification extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Name meta data for Google Site Verification
     */
    const META_NAME = 'google-site-verification';

    /**
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\GoogleShopping\Model\Config $config
     */
    public function __construct(\Magento\Core\Helper\Context $context, \Magento\GoogleShopping\Model\Config $config)
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

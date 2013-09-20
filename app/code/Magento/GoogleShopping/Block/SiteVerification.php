<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google site verification <meta> tag
 */
class Magento_GoogleShopping_Block_SiteVerification extends Magento_Core_Block_Abstract
{
    /** @var Magento_GoogleShopping_Model_Config */
    protected $_config;

    /**
     * @param Magento_Core_Block_Context $context
     * @param Magento_GoogleShopping_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Context $context,
        Magento_GoogleShopping_Model_Config $config,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function _toHtml()
    {
        return ($content = $this->_config->getConfigData('verify_meta_tag'))
            ? '<meta name="google-site-verification" content="' . $this->escapeHtml($content) . '"/>'
            : '';
    }
}

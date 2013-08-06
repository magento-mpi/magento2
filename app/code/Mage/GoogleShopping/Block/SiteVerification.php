<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_GoogleShopping_Block_SiteVerification extends Mage_Core_Block_Abstract
{
    /** @var Mage_GoogleShopping_Model_Config */
    protected $_config;

    /**
     * @param Mage_Core_Block_Context $context
     * @param Mage_GoogleShopping_Model_Config $config
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Context $context,
        Mage_GoogleShopping_Model_Config $config,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return ($content = $this->_config->getConfigData('verify_meta_tag'))
            ? '<meta name="google-site-verification" content="' . $this->escapeHtml($content) . '"/>'
            : '';
    }
}

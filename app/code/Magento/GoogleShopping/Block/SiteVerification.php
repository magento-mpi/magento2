<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Block;

use Magento\View\Element\AbstractBlock;

/**
 * Google site verification <meta> tag
 */
class SiteVerification extends \Magento\View\Element\AbstractBlock
{
    /** @var \Magento\GoogleShopping\Model\Config */
    protected $_config;

    /**
     * @param \Magento\View\Element\Context $context
     * @param \Magento\GoogleShopping\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Context $context,
        \Magento\GoogleShopping\Model\Config $config,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_config = $config;
    }

    /**
     * {@inheritdoc}
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

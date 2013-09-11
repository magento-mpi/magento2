<?php
/**
 * Google AdWords Code block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleAdwords\Block;

class Code extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\GoogleAdwords\Helper\Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\GoogleAdwords\Helper\Data $helper
     */
    public function __construct(\Magento\Core\Block\Template\Context $context, \Magento\GoogleAdwords\Helper\Data $helper)
    {
        parent::__construct($context);
        $this->_helper = $helper;
    }

    /**
     * Render block html if Google AdWords is active
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_helper->isGoogleAdwordsActive() ? parent::_toHtml() : '';
    }

    /**
     * @return \Magento\GoogleAdwords\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}

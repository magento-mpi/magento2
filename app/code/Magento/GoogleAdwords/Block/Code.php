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

class Code extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\GoogleAdwords\Helper\Data
     */
    protected $_googleAdwordsData;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\GoogleAdwords\Helper\Data $googleAdwordsData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\GoogleAdwords\Helper\Data $googleAdwordsData,
        array $data = array()
    ) {
        $this->_googleAdwordsData = $googleAdwordsData;
        parent::__construct($context, $data);
    }

    /**
     * Render block html if Google AdWords is active
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_googleAdwordsData->isGoogleAdwordsActive() ? parent::_toHtml() : '';
    }

    /**
     * @return \Magento\GoogleAdwords\Helper\Data
     */
    public function getHelper()
    {
        return $this->_googleAdwordsData;
    }
}

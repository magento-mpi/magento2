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
    protected $_googleAdwordsData;

    /**
     * @param \Magento\GoogleAdwords\Helper\Data $googleAdwordsData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GoogleAdwords\Helper\Data $googleAdwordsData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_googleAdwordsData = $googleAdwordsData;
        parent::__construct($coreData, $context, $data);
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

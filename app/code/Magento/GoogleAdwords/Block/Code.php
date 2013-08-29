<?php
/**
 * Google AdWords Code block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleAdwords_Block_Code extends Magento_Core_Block_Template
{
    /**
     * @var Magento_GoogleAdwords_Helper_Data
     */
    protected $_googleAdwordsData;

    /**
     * @param Magento_GoogleAdwords_Helper_Data $googleAdwordsData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GoogleAdwords_Helper_Data $googleAdwordsData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_googleAdwordsData = $googleAdwordsData;
        parent::__construct($coreData, $data, $context);
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
     * @return Magento_GoogleAdwords_Helper_Data
     */
    public function getHelper()
    {
        return $this->_googleAdwordsData;
    }
}

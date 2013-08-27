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
    protected $_helper;

    /**
     * Constructor
     *
     *
     *
     * @param array $data
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_GoogleAdwords_Helper_Data $helper
     */
    public function __construct(
        array $data,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_GoogleAdwords_Helper_Data $helper
    ) {
        parent::__construct($coreData, $data, $context);
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
     * @return Magento_GoogleAdwords_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}

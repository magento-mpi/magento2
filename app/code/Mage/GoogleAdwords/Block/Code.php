<?php
/**
 * Google AdWords Code block
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleAdwords_Block_Code extends Mage_Core_Block_Template
{
    /**
     * @var Mage_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_GoogleAdwords_Helper_Data $helper
     */
    public function __construct(Mage_Core_Block_Template_Context $context, Mage_GoogleAdwords_Helper_Data $helper)
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
     * @return Mage_GoogleAdwords_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}

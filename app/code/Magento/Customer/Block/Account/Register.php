<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Customer_Block_Account_Register extends Magento_Page_Block_Link
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $session
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $session,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerSession = $session;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_helperFactory->get('Magento_Customer_Helper_Data')->getRegisterUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return '';
        }
        return parent::_toHtml();
    }
}

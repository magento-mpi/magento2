<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Block_Account_Register extends Mage_Page_Block_Link
{
    /**
     * Customer session
     *
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Customer_Model_Session $session
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Customer_Model_Session $session,
        array $data = array()
    )
    {
        parent::__construct($context, $data);
        $this->_customerSession = $session;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_helperFactory->get('Mage_Customer_Helper_Data')->getRegisterUrl();
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

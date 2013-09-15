<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Orders and Returns" link
 */
class Magento_Sales_Block_Guest_Link extends Magento_Page_Block_Link
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerSession = $customerSession;
    }

    /**
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

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Orders and Returns Link
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Guest_Link extends Mage_Page_Block_Link
{
    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Customer_Model_Session $customerSession,
        array $data = array()
    )
    {
        parent::__construct($context, $data);
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

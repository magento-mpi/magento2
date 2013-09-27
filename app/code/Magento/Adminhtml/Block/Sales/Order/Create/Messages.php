<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order create errors block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Messages extends Magento_Adminhtml_Block_Messages
{
    /**
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_sessionQuote;

    /**
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->sessionQuote = $sessionQuote;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return Magento_Core_Block_Messages
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->_sessionQuote->getMessages(true));
        parent::_prepareLayout();
    }

}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout block for showing messages
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Messages extends Magento_Adminhtml_Block_Messages
{
    /**
     * @var Magento_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Message $message
     * @param Magento_Core_Model_Message_CollectionFactory $messageFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Model_Session $backendSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Message $message,
        Magento_Core_Model_Message_CollectionFactory $messageFactory,
        array $data = array()
    ) {
        $this->_backendSession = $backendSession;
        parent::__construct($coreData, $context, $message, $messageFactory, $data);
    }

    /**
     * Prepares layout for current block
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->_backendSession->getMessages(true));
        parent::_prepareLayout();
    }
}

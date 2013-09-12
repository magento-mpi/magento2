<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation data model
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Model_Observer
{
    /**
     * Flag that indicates customer registration page
     *
     * @var boolean
     */
    protected $_flagInCustomerRegistration = false;

    protected $_config;

    public function __construct()
    {
        $this->_config = Mage::getSingleton('Magento_Invitation_Model_Config');
    }

    /**
     * Handler for invitation mass update
     *
     * @param Magento_Simplexml_Element $config
     * @param Magento_Logging_Model_Event $eventModel
     * @return Magento_Logging_Model_Event
     */
    public function postDispatchInvitationMassUpdate($config, $eventModel)
    {
        $messages = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getMessages();
        $errors = $messages->getErrors();
        $notices = $messages->getItemsByType(Magento_Core_Model_Message::NOTICE);
        $status = (empty($errors) && empty($notices))
            ? Magento_Logging_Model_Event::RESULT_SUCCESS : Magento_Logging_Model_Event::RESULT_FAILURE;
        return $eventModel->setStatus($status)
            ->setInfo(Mage::app()->getRequest()->getParam('invitations'));
    }
}

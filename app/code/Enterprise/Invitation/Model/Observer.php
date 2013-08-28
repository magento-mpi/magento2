<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation data model
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Observer
{
    /**
     * Flag that indicates customer registration page
     *
     * @var boolean
     */
    protected $_flagInCustomerRegistration = false;

    /**
     * Invitation configuration
     *
     * @var Enterprise_Invitation_Model_Config
     */
    protected $_config;

    /**
     * Invitation data
     *
     * @var Enterprise_Invitation_Helper_Data
     */
    protected $_invitationData = null;

    /**
     * @param Enterprise_Invitation_Helper_Data $invitationData
     */
    public function __construct(
        Enterprise_Invitation_Helper_Data $invitationData
    ) {
        $this->_invitationData = $invitationData;
        $this->_config = Mage::getSingleton('Enterprise_Invitation_Model_Config');
    }

    /**
     * Observe customer registration for invitations
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function restrictCustomerRegistration(Magento_Event_Observer $observer)
    {
        if (!$this->_config->isEnabledOnFront()) {
            return;
        }

        $result = $observer->getEvent()->getResult();
        if (!$result->getIsAllowed()) {
            $this->_invitationData->isRegistrationAllowed(false);
        } else {
            $this->_invitationData->isRegistrationAllowed(true);
            $result->setIsAllowed(!$this->_config->getInvitationRequired());
        }
    }

    /**
     * Handler for invitation mass update
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchInvitationMassUpdate($config, $eventModel)
    {
        $messages = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getMessages();
        $errors = $messages->getErrors();
        $notices = $messages->getItemsByType(Magento_Core_Model_Message::NOTICE);
        $status = (empty($errors) && empty($notices))
            ? Enterprise_Logging_Model_Event::RESULT_SUCCESS : Enterprise_Logging_Model_Event::RESULT_FAILURE;
        return $eventModel->setStatus($status)
            ->setInfo(Mage::app()->getRequest()->getParam('invitations'));
    }
}

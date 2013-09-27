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

    /**
     * Invitation configuration
     *
     * @var Magento_Invitation_Model_Config
     */
    protected $_config;

    /**
     * Invitation data
     *
     * @var Magento_Invitation_Helper_Data
     */
    protected $_invitationData;

    /**
     * Backend Session
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * Request object
     *
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @param Magento_Invitation_Helper_Data $invitationData
     * @param Magento_Invitation_Model_Config $config
     * @param Magento_Backend_Model_Auth_Session $session
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Invitation_Helper_Data $invitationData,
        Magento_Invitation_Model_Config $config,
        Magento_Backend_Model_Auth_Session $session,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_invitationData = $invitationData;
        $this->_config = $config;
        $this->_session = $session;
        $this->_request = $request;
    }

    /**
     * Handler for invitation mass update
     *
     * @param array $config
     * @param Magento_Logging_Model_Event $eventModel
     * @return Magento_Logging_Model_Event
     */
    public function postDispatchInvitationMassUpdate($config, $eventModel)
    {
        $messages = $this->_session->getMessages();
        $errors = $messages->getErrors();
        $notices = $messages->getItemsByType(Magento_Core_Model_Message::NOTICE);
        $status = (empty($errors) && empty($notices))
            ? Magento_Logging_Model_Event::RESULT_SUCCESS : Magento_Logging_Model_Event::RESULT_FAILURE;
        return $eventModel->setStatus($status)
            ->setInfo($this->_request->getParam('invitations'));
    }
}

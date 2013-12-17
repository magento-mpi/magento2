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
namespace Magento\Invitation\Model;

class Observer
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
     * @var \Magento\Invitation\Model\Config
     */
    protected $_config;

    /**
     * Invitation data
     *
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationData;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Magento\Invitation\Helper\Data $invitationData
     * @param \Magento\Invitation\Model\Config $config
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Invitation\Helper\Data $invitationData,
        \Magento\Invitation\Model\Config $config,
        \Magento\Message\ManagerInterface $messageManager,
        \Magento\App\RequestInterface $request
    ) {
        $this->_invitationData = $invitationData;
        $this->_config = $config;
        $this->messageManager = $messageManager;
        $this->_request = $request;
    }

    /**
     * Handler for invitation mass update
     *
     * @param array $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event
     */
    public function postDispatchInvitationMassUpdate($config, $eventModel)
    {
        $messages = $this->messageManager->getMessages();
        $errors = $messages->getErrors();
        $notices = $messages->getItemsByType(\Magento\Message\MessageInterface::TYPE_NOTICE);
        $status = (empty($errors) && empty($notices))
            ? \Magento\Logging\Model\Event::RESULT_SUCCESS : \Magento\Logging\Model\Event::RESULT_FAILURE;
        return $eventModel->setStatus($status)
            ->setInfo($this->_request->getParam('invitations'));
    }
}

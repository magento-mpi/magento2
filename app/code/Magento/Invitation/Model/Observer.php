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
    protected $_invitationData = null;

    /**
     * @param \Magento\Invitation\Helper\Data $invitationData
     */
    public function __construct(
        \Magento\Invitation\Helper\Data $invitationData
    ) {
        $this->_invitationData = $invitationData;
        $this->_config = \Mage::getSingleton('Magento\Invitation\Model\Config');
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
        $messages = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getMessages();
        $errors = $messages->getErrors();
        $notices = $messages->getItemsByType(\Magento\Core\Model\Message::NOTICE);
        $status = (empty($errors) && empty($notices))
            ? \Magento\Logging\Model\Event::RESULT_SUCCESS : \Magento\Logging\Model\Event::RESULT_FAILURE;
        return $eventModel->setStatus($status)
            ->setInfo(\Mage::app()->getRequest()->getParam('invitations'));
    }
}

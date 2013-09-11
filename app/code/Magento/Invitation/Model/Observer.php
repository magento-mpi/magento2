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

    protected $_config;

    public function __construct()
    {
        $this->_config = \Mage::getSingleton('Magento\Invitation\Model\Config');
    }

    /**
     * Observe customer registration for invitations
     *
     * @return void
     */
    public function restrictCustomerRegistration(\Magento\Event\Observer $observer)
    {
        if (!$this->_config->isEnabledOnFront()) {
            return;
        }

        $result = $observer->getEvent()->getResult();

        if (!$result->getIsAllowed()) {
            \Mage::helper('Magento\Invitation\Helper\Data')->isRegistrationAllowed(false);
        } else {
            \Mage::helper('Magento\Invitation\Helper\Data')->isRegistrationAllowed(true);
            $result->setIsAllowed(!$this->_config->getInvitationRequired());
        }
    }

    /**
     * Handler for invitation mass update
     *
     * @param \Magento\Simplexml\Element $config
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

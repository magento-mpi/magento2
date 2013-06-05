<?php
/**
 * Observer for applying limitations related to number of users
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_User_Observer
{
    /**
     * @var Saas_Limitation_Model_User_Limitation
     */
    private $_limitation;

    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @param Saas_Limitation_Model_User_Limitation $limitation
     * @param Mage_Backend_Model_Session $session
     */
    public function __construct(
        Saas_Limitation_Model_User_Limitation $limitation,
        Mage_Backend_Model_Session $session
    ) {
        $this->_limitation = $limitation;
        $this->_session = $session;
    }

    /**
     * Disable button for creation new user, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableCreationButton(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Mage_User_Block_User && $this->_limitation->isCreateRestricted()) {
            $block->updateButton('add', 'disabled', true);
        }
    }

    /**
     * Add restriction message to the session, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function displayNotification(Varien_Event_Observer $observer)
    {
        if ($this->_limitation->isCreateRestricted()) {
            $this->_session->addNotice($this->_limitation->getCreateRestrictedMessage());
        }
    }

    /**
     * Restrict creation of new entity, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_User_Model_User $user */
        $user = $observer->getEvent()->getData('data_object');
        if ($user->isObjectNew() && $this->_limitation->isCreateRestricted()) {
            $message = $this->_limitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    private $_request;

    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @var Saas_Limitation_Model_Store_Limitation
     */
    private $_storeLimitation;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Backend_Model_Session $session
     * @param Saas_Limitation_Model_Store_Limitation $storeLimitation
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Backend_Model_Session $session,
        Saas_Limitation_Model_Store_Limitation $storeLimitation
    ) {
        $this->_request = $request;
        $this->_session = $session;
        $this->_storeLimitation = $storeLimitation;
    }

    /**
     * Display message in the notification area upon reaching limitations
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictStoreCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Abstract $model */
        $model = $observer->getEvent()->getData('store');
        if ($model->isObjectNew() && $this->_storeLimitation->isCreateRestricted()) {
            $message = $this->_storeLimitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }

    /**
     * Remove/disable grid buttons upon reaching limitations
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateGridButtons(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Mage_Adminhtml_Block_System_Store_Store) {
            if ($this->_storeLimitation->isCreateRestricted()) {
                $block->updateButton('add_store', 'disabled', true);
            }
        }
    }

    /**
     * Display message in the notification area upon reaching limitations
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function displayNotification(Varien_Event_Observer $observer)
    {
        if ($this->_getControllerAction() == 'Mage_Adminhtml/system_store/index') {
            if ($this->_storeLimitation->isCreateRestricted()) {
                $this->_session->addNotice($this->_storeLimitation->getCreateRestrictedMessage());
            }
        }
    }

    /**
     * Retrieve a unique identifier for the current controller action
     *
     * @return string
     */
    protected function _getControllerAction()
    {
        return $this->_request->getControllerModule()
            . '/' . $this->_request->getControllerName()
            . '/' . $this->_request->getActionName();
    }
}

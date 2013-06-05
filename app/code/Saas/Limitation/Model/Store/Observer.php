<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Observer
{
    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @var Saas_Limitation_Model_Store_Limitation
     */
    private $_storeLimitation;

    /**
     * @param Mage_Backend_Model_Session $session
     * @param Saas_Limitation_Model_Store_Limitation $storeLimitation
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Saas_Limitation_Model_Store_Limitation $storeLimitation
    ) {
        $this->_session = $session;
        $this->_storeLimitation = $storeLimitation;
    }

    /**
     * Restrict creation of new stores upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Abstract $model */
        $model = $observer->getEvent()->getData('data_object');
        if ($model->isObjectNew() && $this->_storeLimitation->isCreateRestricted()) {
            $message = $this->_storeLimitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }

    /**
     * Disable the store creation button in the grid upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableCreationButton(Varien_Event_Observer $observer)
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
     * Display message in the notification area upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function displayNotification(Varien_Event_Observer $observer)
    {
        if ($this->_storeLimitation->isCreateRestricted()) {
            $this->_session->addNotice($this->_storeLimitation->getCreateRestrictedMessage());
        }
    }
}

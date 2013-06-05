<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_Group_Observer
{
    /**
     * @var Saas_Limitation_Model_Store_Group_Limitation
     */
    private $_limitation;

    /**
     * @param Saas_Limitation_Model_Store_Group_Limitation $limitation
     */
    public function __construct(Saas_Limitation_Model_Store_Group_Limitation $limitation)
    {
        $this->_limitation = $limitation;
    }

    /**
     * Restrict creation of new store groups upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Abstract $model */
        $model = $observer->getEvent()->getData('data_object');
        if ($model->isObjectNew() && $this->_limitation->isCreateRestricted()) {
            $message = $this->_limitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }

    /**
     * Disable the store group creation button in the grid upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeCreationButton(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Mage_Adminhtml_Block_System_Store_Store) {
            if ($this->_limitation->isCreateRestricted()) {
                $block->removeButton('add_group');
            }
        }
    }
}

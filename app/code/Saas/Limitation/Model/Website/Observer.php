<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Website_Observer
{
    /**
     * @var Saas_Limitation_Model_Website_Limitation
     */
    protected $_websiteLimitation;

    /**
     * @param Saas_Limitation_Model_Website_Limitation $websiteLimitation
     */
    public function __construct(
        Saas_Limitation_Model_Website_Limitation $websiteLimitation
    ) {
        $this->_websiteLimitation = $websiteLimitation;
    }

    /*
     * Display message in the notification area upon reaching limitation
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Website $website */
        $website = $observer->getWebsite();
        if ($website->isObjectNew() && $this->_websiteLimitation->isCreateRestricted()) {
            $errorMessage = $this->_websiteLimitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($errorMessage);
            $exception->addMessage(new Mage_Core_Model_Message_Error($errorMessage));
            throw $exception;
        }
    }

    /**
     * Remove 'Add Website' grid button upon reaching limitations
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeCreationButton(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_System_Store_Store) {
            if ($this->_websiteLimitation->isCreateRestricted()) {
                $block->removeButton('add');
            }
        }
    }
}

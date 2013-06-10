<?php
/**
 * Observer for applying limitations related to number of products
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer
{
    /**
     * @var Saas_Limitation_Model_Catalog_Product_Limitation
     */
    private $_limitation;

    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @param Saas_Limitation_Model_Catalog_Product_Limitation $limitation
     * @param Mage_Backend_Model_Session $session
     */
    public function __construct(
        Saas_Limitation_Model_Catalog_Product_Limitation $limitation,
        Mage_Backend_Model_Session $session
    ) {
        $this->_limitation = $limitation;
        $this->_session = $session;
    }

    /**
     * Restrict creation of new entity, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_User_Model_User $entity */
        $entity = $observer->getEvent()->getData('product');
        if ($entity->isObjectNew() && $this->_limitation->isCreateRestricted()) {
            $message = $this->_limitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
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
     * @param Varien_Event_Observer $observer
     */
    public function disableCreationButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product) {
            if ($this->_limitation->isCreateRestricted()) {
                $block->updateButton('add_new', 'disabled', true);
                $block->updateButton('add_new', 'has_split', false);
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function removeRestrictedSavingButtons(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit) {
            $product = $block->getProduct();
            $creationRestricted = $this->_limitation->isCreateRestricted(!$product || $product->isObjectNew() ? 2 : 1);
            if ($creationRestricted) {
                /** @var Mage_Backend_Block_Widget_Button_Split $child */
                $child = $block->getChildBlock('save-split-button');
                $restrictedButtons = array('new-button', 'duplicate-button');
                $filteredOptions = array();
                foreach ($child->getOptions() as $option) {
                    if (!in_array($option['id'], $restrictedButtons)) {
                        $filteredOptions[] = $option;
                    }
                }
                $child->setData('options', $filteredOptions);
            }
        }
    }
}

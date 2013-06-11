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
     * @var Saas_Limitation_Helper_Data
     */
    private $_helper;

    /**
     * @param Saas_Limitation_Model_Catalog_Product_Limitation $limitation
     * @param Mage_Backend_Model_Session $session
     * @param Saas_Limitation_Helper_Data $helper
     */
    public function __construct(
        Saas_Limitation_Model_Catalog_Product_Limitation $limitation,
        Mage_Backend_Model_Session $session,
        Saas_Limitation_Helper_Data $helper
    ) {
        $this->_limitation = $limitation;
        $this->_session = $session;
        $this->_helper = $helper;
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
            $message = $this->_getNotificationMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }

    /**
     * Restrict creation of new entity, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Catalog_Exception
     */
    public function restrictEntityCreationWithVariations(Varien_Event_Observer $observer)
    {
        /** @var Mage_User_Model_User $entity */
        $entity = $observer->getEvent()->getData('product');
        $variations = $observer->getEvent()->getData('variations');
        $newEntities = ($entity->isObjectNew() ? 1 : 0) + count($variations);
        if ($newEntities > 0 && $this->_limitation->isCreateRestricted($newEntities)) {
            // @codingStandardsIgnoreStart
            $message = $this->__('We could not save the product. You tried to add %d products, but the most you can have is %d. To add more, please upgrade your service.');
            // @codingStandardsIgnoreEnd
            $message = sprintf($message, $newEntities, $this->_limitation->getLimit());
            throw new Mage_Catalog_Exception($message);
        }
    }

    /**
     * Restrict duplication of an entity, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function restrictEntityDuplication(Varien_Event_Observer $observer)
    {
        if ($this->_limitation->isCreateRestricted()) {
            $message = $this->_getCreationRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }

    /**
     * Restrict redirect to new product creation page, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Catalog_Exception
     */
    public function restrictNewEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Catalog_ProductController $controller */
        $controller = $observer->getEvent()->getData('controller');
        if ($controller->getRequest()->getParam('back') == 'new') {
            if ($this->_limitation->isCreateRestricted()) {
                $message = $this->_getCreationRestrictedMessage();
                throw new Mage_Catalog_Exception($message);
            }
        }
    }

    /**
     * Add notification message to the session, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function displayNotification(Varien_Event_Observer $observer)
    {
        if ($this->_limitation->isCreateRestricted()) {
            $this->_session->addNotice($this->_getNotificationMessage());
        }
    }

    /**
     * Disable creation product button, if the limitation is reached
     *
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
     * Remove restricted buttons, if the limitation is reached
     * Buttons are considered as restricted, if they let create new entity
     *
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

    /**
     * Get message with notification that the limitation is reached
     *
     * @return string
     */
    protected function _getNotificationMessage()
    {
        // @codingStandardsIgnoreStart
        return $this->__('Sorry, you are using all the products and variations your account allows. To add more, first delete a product or upgrade your service.');
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get message about restriction to create new product
     *
     * @return string
     */
    protected function _getCreationRestrictedMessage()
    {
        return $this->__("You can't create new product.");
    }

    /**
     * Get translation of the text
     *
     * @param string $text
     * @return string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    protected function __($text)
    {
        return $this->_helper->__($text);
    }
}

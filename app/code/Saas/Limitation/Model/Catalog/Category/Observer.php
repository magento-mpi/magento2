<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Category_Observer
{
    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @var Saas_Limitation_Model_Catalog_Category_Limitation
     */
    private $_categoryLimitation;

    /**
     * @param Mage_Backend_Model_Session $session
     * @param Saas_Limitation_Model_Catalog_Category_Limitation $categoryLimitation
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Saas_Limitation_Model_Catalog_Category_Limitation $categoryLimitation
    ) {
        $this->_session = $session;
        $this->_categoryLimitation = $categoryLimitation;
    }

    /**
     * Restrict creation of new categories upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function restrictEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Abstract $model */
        $model = $observer->getEvent()->getData('data_object');
        if ($model->isObjectNew() && $this->_categoryLimitation->isCreateRestricted()) {
            $message = $this->_categoryLimitation->getCreateRestrictedMessage();
            $exception = new Mage_Core_Exception($message);
            $exception->addMessage(new Mage_Core_Model_Message_Error($message));
            throw $exception;
        }
    }

    /**
     * Disable the category creation buttons upon reaching the limitation
     * Buttons are disabled in tree and in form on category page and on product edit page
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableCreationButtons(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tree) {
            $this->_disableButtonsInCategoryTree($block);
        } elseif ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Edit_Form) {
            $this->_disableButtonsInCategoryForm($block);
        } elseif ($block instanceof Mage_Backend_Block_Widget_Button) {
            $this->_disableButtonsInProduct($block);
        }
    }

    /**
     * Disable buttons in category tree block
     *
     * @param $block
     */
    protected function _disableButtonsInCategoryTree(Mage_Adminhtml_Block_Catalog_Category_Tree $block)
    {
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tree) {
            if ($this->_categoryLimitation->isCreateRestricted()) {
                foreach (array('add_root_button', 'add_sub_button') as $buttonName) {
                    $button = $block->getChildBlock($buttonName);
                    if ($button instanceof Mage_Backend_Block_Widget_Button) {
                        $button->setData('disabled', true);
                    }
                }
            }
        }
    }

    /**
     * Disable Save button in form on category page
     *
     * @param Mage_Adminhtml_Block_Catalog_Category_Edit_Form $block
     */
    protected function _disableButtonsInCategoryForm(Mage_Adminhtml_Block_Catalog_Category_Edit_Form $block)
    {
        $categoryId = $block->getCategoryId();

        if ($this->_categoryLimitation->isCreateRestricted() && is_null($categoryId)) {
            $button = $block->getChildBlock('save_button');

            if ($button instanceof Mage_Backend_Block_Widget_Button) {
                $button->setData('disabled', true);
            }
        }
    }

    /**
     * Disable button "New Category" on product edit page
     *
     * @param Mage_Backend_Block_Widget_Button $block
     */
    protected function _disableButtonsInProduct(Mage_Backend_Block_Widget_Button $block)
    {
        if (($block->getId() === 'add_category_button') && $this->_categoryLimitation->isCreateRestricted()) {
            $block->setData('disabled', true);
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
        if ($this->_categoryLimitation->isCreateRestricted()) {
            $this->_session->addNotice($this->_categoryLimitation->getCreateRestrictedMessage());
        }
    }
}

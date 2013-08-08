<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Category_Observer_Block
{
    /**
     * @var Saas_Limitation_Model_Limitation_Validator
     */
    private $_limitationValidator;

    /**
     * @var Saas_Limitation_Model_Limitation_LimitationInterface
     */
    private $_limitation;

    /**
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator
     * @param Saas_Limitation_Model_Limitation_LimitationInterface $limitation
     */
    public function __construct(
        Saas_Limitation_Model_Limitation_Validator $limitationValidator,
        Saas_Limitation_Model_Limitation_LimitationInterface $limitation
    ) {
        $this->_limitationValidator = $limitationValidator;
        $this->_limitation = $limitation;
    }

    /**
     * Disable the category creation buttons upon reaching the limitation
     * Buttons are disabled in tree and in form on category page and on product edit page
     *
     * @param Magento_Event_Observer $observer
     */
    public function disableCreationButtons(Magento_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Magento_Adminhtml_Block_Catalog_Category_Tree) {
            $this->_disableButtonsInCategoryTree($block);
        } else if ($block instanceof Magento_Adminhtml_Block_Catalog_Category_Edit_Form) {
            $this->_disableButtonsInCategoryForm($block);
        } else if ($block instanceof Mage_Backend_Block_Widget_Button) {
            $this->_disableButtonsInProduct($block);
        }
    }

    /**
     * Whether a limitation threshold has been reached
     *
     * @return bool
     */
    protected function _isThresholdReached()
    {
        return $this->_limitationValidator->exceedsThreshold($this->_limitation);
    }

    /**
     * Disable buttons in category tree block
     *
     * @param $block
     */
    protected function _disableButtonsInCategoryTree(Magento_Adminhtml_Block_Catalog_Category_Tree $block)
    {
        if ($this->_isThresholdReached()) {
            $this->_disableChildButtons($block, array('add_root_button', 'add_sub_button'));
        }
    }

    /**
     * Disable Save button in form on category page
     *
     * @param Magento_Adminhtml_Block_Catalog_Category_Edit_Form $block
     */
    protected function _disableButtonsInCategoryForm(Magento_Adminhtml_Block_Catalog_Category_Edit_Form $block)
    {
        if ($block->getCategoryId() === null && $this->_isThresholdReached()) {
            $this->_disableChildButtons($block, array('save_button'));
        }
    }

    /**
     * Disable button "New Category" on product edit page
     *
     * @param Mage_Backend_Block_Widget_Button $block
     */
    protected function _disableButtonsInProduct(Mage_Backend_Block_Widget_Button $block)
    {
        if ($block->getId() === 'add_category_button' && $this->_isThresholdReached()) {
            $this->_disableButton($block);
        }
    }

    /**
     * Disable child buttons of a block
     *
     * @param Magento_Core_Block_Abstract $block
     * @param array $buttonNames
     */
    protected function _disableChildButtons(Magento_Core_Block_Abstract $block, array $buttonNames)
    {
        foreach ($buttonNames as $buttonName) {
            $button = $block->getChildBlock($buttonName);
            if ($button instanceof Mage_Backend_Block_Widget_Button) {
                $this->_disableButton($button);
            }
        }
    }

    /**
     * Disable a button
     *
     * @param Mage_Backend_Block_Widget_Button $block
     */
    protected function _disableButton(Mage_Backend_Block_Widget_Button $block)
    {
        $block->setData('disabled', true);
    }
}

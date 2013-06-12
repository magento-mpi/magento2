<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Form
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
     * Remove restricted buttons, if the limitation is reached
     * Buttons are considered as restricted, if they let create new entity
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeSavingButtons(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit) {
            $product = $block->getProduct();
            $isThresholdReached = $this->_limitationValidator->isThresholdReached(
                $this->_limitation,
                !$product || $product->isObjectNew() ? 2 : 1
            );
            if ($isThresholdReached) {
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

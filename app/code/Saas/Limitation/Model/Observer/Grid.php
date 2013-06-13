<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer_Grid
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
     * @var string
     */
    private $_blockClass;

    /**
     * @var string
     */
    private $_buttonId;

    /**
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator
     * @param Saas_Limitation_Model_Limitation_LimitationInterface $limitation
     * @param string $blockClass
     * @param string $buttonId
     */
    public function __construct(
        Saas_Limitation_Model_Limitation_Validator $limitationValidator,
        Saas_Limitation_Model_Limitation_LimitationInterface $limitation,
        $blockClass,
        $buttonId
    ) {
        $this->_limitationValidator = $limitationValidator;
        $this->_limitation = $limitation;
        $this->_blockClass = $blockClass;
        $this->_buttonId = $buttonId;
    }

    /**
     * Whether a block is an instance of an expected class
     *
     * @param Mage_Backend_Block_Widget_Container $block
     * @return bool
     */
    protected function _isRelevantBlock(Mage_Backend_Block_Widget_Container $block)
    {
        return ($block instanceof $this->_blockClass);
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
     * Disable a button in the grid upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableButton(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getEvent()->getData('block');
        if ($this->_isRelevantBlock($block) && $this->_isThresholdReached()) {
            $block->updateButton($this->_buttonId, 'disabled', true);
        }
    }

    /**
     * Disable a split-style button in the grid upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableSplitButton(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getEvent()->getData('block');
        if ($this->_isRelevantBlock($block) && $this->_isThresholdReached()) {
            $block->updateButton($this->_buttonId, 'disabled', true);
            $block->updateButton($this->_buttonId, 'has_split', false);
        }
    }

    /**
     * Remove a button from the grid upon reaching the limitation
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeButton(Varien_Event_Observer $observer)
    {
        /** @var Mage_Backend_Block_Widget_Container $block */
        $block = $observer->getEvent()->getData('block');
        if ($this->_isRelevantBlock($block) && $this->_isThresholdReached()) {
            $block->removeButton($this->_buttonId);
        }
    }
}

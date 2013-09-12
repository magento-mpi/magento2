<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder adminhtml promo rules notice block
 */
class Magento_Reminder_Block_Adminhtml_Promo_Notice extends Magento_Adminhtml_Block_Template
{
    /**
     * Core registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Preparing block layout
     *
     * @return Magento_Reminder_Block_Adminhtml_Promo_Notice
     */
    protected function _prepareLayout()
    {
        if ($salesRule = $this->_coreRegistry->registry('current_promo_quote_rule')) {
            $resource = Mage::getResourceModel('Magento_Reminder_Model_Resource_Rule');
            if ($count = $resource->getAssignedRulesCount($salesRule->getId())) {
                $confirm = __('This rule is assigned to %1 automated reminder rule(s). Deleting this rule will automatically unassign it.', $count);
                $block = $this->getLayout()->getBlock('promo_quote_edit');
                if ($block instanceof Magento_Adminhtml_Block_Promo_Quote_Edit) {
                    $block->updateButton('delete', 'onclick', 'deleteConfirm(\'' . $confirm . '\', \'' . $block->getDeleteUrl() . '\')');
                }
            }
        }
        return $this;
    }
}

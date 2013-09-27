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
     * Rule Resource Model
     *
     * @var Magento_Reminder_Model_Resource_Rule
     */
    protected $_resourceModel;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Reminder_Model_Resource_Rule $resourceModel
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Reminder_Model_Resource_Rule $resourceModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_coreRegistry = $registry;
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Preparing block layout
     *
     * @return Magento_Reminder_Block_Adminhtml_Promo_Notice
     */
    protected function _prepareLayout()
    {
        if ($salesRule = $this->_coreRegistry->registry('current_promo_quote_rule')) {
            if ($count = $this->_resourceModel->getAssignedRulesCount($salesRule->getId())) {
                $confirm = __('This rule is assigned to %1 automated reminder rule(s). Deleting this rule will automatically unassign it.',
                    $count);
                $block = $this->getLayout()->getBlock('promo_quote_edit');
                if ($block instanceof Magento_Adminhtml_Block_Promo_Quote_Edit) {
                    $block->updateButton(
                        'delete', 'onclick', 'deleteConfirm(\'' . $confirm . '\', \'' . $block->getDeleteUrl() . '\')');
                }
            }
        }
        return $this;
    }
}

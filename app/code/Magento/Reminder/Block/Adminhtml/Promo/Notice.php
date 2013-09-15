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
namespace Magento\Reminder\Block\Adminhtml\Promo;

class Notice extends \Magento\Adminhtml\Block\Template
{
    /**
     * Core registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Preparing block layout
     *
     * @return \Magento\Reminder\Block\Adminhtml\Promo\Notice
     */
    protected function _prepareLayout()
    {
        if ($salesRule = $this->_coreRegistry->registry('current_promo_quote_rule')) {
            $resource = \Mage::getResourceModel('Magento\Reminder\Model\Resource\Rule');
            if ($count = $resource->getAssignedRulesCount($salesRule->getId())) {
                $confirm = __('This rule is assigned to %1 automated reminder rule(s). Deleting this rule will automatically unassign it.', $count);
                $block = $this->getLayout()->getBlock('promo_quote_edit');
                if ($block instanceof \Magento\Adminhtml\Block\Promo\Quote\Edit) {
                    $block->updateButton('delete', 'onclick', 'deleteConfirm(\'' . $confirm . '\', \'' . $block->getDeleteUrl() . '\')');
                }
            }
        }
        return $this;
    }
}

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
     * Preparing block layout
     *
     * @return \Magento\Reminder\Block\Adminhtml\Promo\Notice
     */
    protected function _prepareLayout()
    {
        if ($salesRule = \Mage::registry('current_promo_quote_rule')) {
            $resource = \Mage::getResourceModel('\Magento\Reminder\Model\Resource\Rule');
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

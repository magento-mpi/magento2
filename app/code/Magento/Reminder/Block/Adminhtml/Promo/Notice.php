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
     * Rule Resource Model
     *
     * @var \Magento\Reminder\Model\Resource\Rule
     */
    protected $_resourceModel;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Reminder\Model\Resource\Rule $resourceModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Reminder\Model\Resource\Rule $resourceModel,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_coreRegistry = $registry;
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Preparing block layout
     *
     * @return \Magento\Reminder\Block\Adminhtml\Promo\Notice
     */
    protected function _prepareLayout()
    {
        if ($salesRule = $this->_coreRegistry->registry('current_promo_quote_rule')) {
            if ($count = $this->_resourceModel->getAssignedRulesCount($salesRule->getId())) {
                $confirm = __('This rule is assigned to %1 automated reminder rule(s). Deleting this rule will automatically unassign it.',
                    $count);
                $block = $this->getLayout()->getBlock('promo_quote_edit');
                if ($block instanceof \Magento\Adminhtml\Block\Promo\Quote\Edit) {
                    $block->updateButton(
                        'delete', 'onclick', 'deleteConfirm(\'' . $confirm . '\', \'' . $block->getDeleteUrl() . '\')');
                }
            }
        }
        return $this;
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Reminder adminhtml promo rules notice block
 */
namespace Magento\Reminder\Block\Adminhtml\Promo;

class Notice extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Rule Resource Model
     *
     * @var \Magento\Reminder\Model\Resource\Rule
     */
    protected $_resourceModel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Reminder\Model\Resource\Rule $resourceModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Reminder\Model\Resource\Rule $resourceModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_resourceModel = $resourceModel;
    }

    /**
     * Preparing block layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($salesRule = $this->_coreRegistry->registry('current_promo_quote_rule')) {
            if ($count = $this->_resourceModel->getAssignedRulesCount($salesRule->getId())) {
                $confirm = __(
                    'This rule is assigned to %1 automated reminder rule(s). Deleting this rule will automatically unassign it.',
                    $count
                );
                $block = $this->getLayout()->getBlock('promo_quote_edit');
                if ($block instanceof \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit) {
                    $block->updateButton(
                        'delete',
                        'onclick',
                        'deleteConfirm(\'' . $confirm . '\', \'' . $block->getDeleteUrl() . '\')'
                    );
                }
            }
        }
        return $this;
    }
}

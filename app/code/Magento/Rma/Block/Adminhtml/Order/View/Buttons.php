<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Order\View;

/**
 * Additional buttons on order view page
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Buttons extends \Magento\Sales\Block\Adminhtml\Order\View
{
    const CREATE_RMA_BUTTON_DEFAULT_SORT_ORDER = 35;

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Rma\Helper\Data $rmaData,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    /**
     * Add button to Shopping Cart Management etc.
     *
     * @return $this
     */
    public function addButtons()
    {
        if ($this->_isCreateRmaButtonRequired()) {
            $parentBlock = $this->getParentBlock();
            $buttonUrl = $this->_urlBuilder->getUrl(
                'adminhtml/rma/new',
                array('order_id' => $parentBlock->getOrderId())
            );

            $this->getToolbar()->addChild(
                'create_rma',
                'Magento\Backend\Block\Widget\Button',
                array('label' => __('Create Returns'), 'onclick' => 'setLocation(\'' . $buttonUrl . '\')')
            );
        }
        return $this;
    }

    /**
     * Check if 'Create RMA' button has to be displayed
     *
     * @return boolean
     */
    protected function _isCreateRmaButtonRequired()
    {
        $parentBlock = $this->getParentBlock();
        return $parentBlock instanceof \Magento\Backend\Block\Template &&
            $parentBlock->getOrderId() &&
            $this->_rmaData->canCreateRma(
                $parentBlock->getOrder(),
                true
            );
    }
}

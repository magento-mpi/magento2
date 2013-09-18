<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional buttons on order view page
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Order\View;

class Buttons extends \Magento\Adminhtml\Block\Sales\Order\View
{
    const CREATE_RMA_BUTTON_DEFAULT_SORT_ORDER = 35;

    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $registry, $coreConfig, $data);
    }

    /**
     * Add button to Shopping Cart Management etc.
     *
     * @return \Magento\Rma\Block\Adminhtml\Order\View\Buttons
     */
    public function addButtons()
    {
        if ($this->_isCreateRmaButtonRequired()) {
            $parentBlock = $this->getParentBlock();
            $buttonUrl = $this->_urlBuilder->getUrl('*/rma/new', array('order_id' => $parentBlock->getOrderId()));
            $parentBlock->addButton('create_rma', array(
                'label' => __('Create Returns'),
                'onclick' => 'setLocation(\'' . $buttonUrl . '\')',
            ), 0, $this->_getCreateRmaButtonSortOrder());
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
        return $parentBlock instanceof \Magento\Backend\Block\Template
            && $parentBlock->getOrderId()
            && $this->_rmaData->canCreateRma($parentBlock->getOrder(), true);
    }

    /**
     * Retrieve sort order of 'Create RMA' button
     *
     * @return int
     */
    protected function _getCreateRmaButtonSortOrder()
    {
        $sortOrder = self::CREATE_RMA_BUTTON_DEFAULT_SORT_ORDER;
        // 'Create RMA' button has to be placed after 'Send Email' button
        if (isset($this->_buttons[0]['send_notification']['sort_order'])) {
            $sortOrder = $this->_buttons[0]['send_notification']['sort_order'] + 5;
        }
        return $sortOrder;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invoice view  comments form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Comments;

class View extends \Magento\Adminhtml\Block\Template
{
    /**
     * Sales data
     *
     * @var Magento_Sales_Helper_Data
     */
    protected $_salesData = null;

    /**
     * @param Magento_Sales_Helper_Data $salesData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Helper_Data $salesData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_salesData = $salesData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            \Mage::throwException(__('Please correct the parent block for this block.'));
        }
        $this->setEntity($this->getParentBlock()->getSource());
        parent::_beforeToHtml();
    }

    /**
     * Prepare child blocks
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\Invoice\Create\Items
     */
    protected function _prepareLayout()
    {
        $this->addChild('submit_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'id'      => 'submit_comment_button',
            'label'   => __('Submit Comment'),
            'class'   => 'save'
        ));
        return parent::_prepareLayout();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('id' => $this->getEntity()->getId()));
    }

    public function canSendCommentEmail()
    {
        switch ($this->getParentType()) {
            case 'invoice':
                return $this->_salesData
                    ->canSendInvoiceCommentEmail($this->getEntity()->getOrder()->getStore()->getId());
            case 'shipment':
                return $this->_salesData
                    ->canSendShipmentCommentEmail($this->getEntity()->getOrder()->getStore()->getId());
            case 'creditmemo':
                return $this->_salesData
                    ->canSendCreditmemoCommentEmail($this->getEntity()->getOrder()->getStore()->getId());
        }
        return true;
    }
}

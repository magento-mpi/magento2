<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma;

class NewRma extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Initialize RMA new page. Set management buttons
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'Magento_Rma';

        parent::_construct();

        $this->_updateButton('reset', 'label', __('Cancel'));
        $this->_updateButton('reset', 'class', 'cancel');

        $link = $this->getUrl('*/*/');
        $order = $this->_coreRegistry->registry('current_order');

        if ($order && $order->getId()) {
            $orderId    = $order->getId();
            $referer    = $this->getRequest()->getServer('HTTP_REFERER');

            if (strpos($referer, 'customer') !== false) {
                $link = $this->getUrl('*/customer/edit/',
                    array(
                        'id'  => $order->getCustomerId(),
                        'active_tab'=> 'orders'
                    )
                );
            }
        } else {
            return;
        }

        if ($this->_rmaData->canCreateRma($orderId, true)) {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_updateButton('save', 'label', __('Submit Returns'));
        } else {
            $this->_updateButton('reset', 'onclick', "setLocation('" . $link . "')");
            $this->_removeButton('save');
        }
        $this->_removeButton('back');
    }

    /**
     * Get header text for RMA edit page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->getLayout()->createBlock('Magento\Rma\Block\Adminhtml\Rma\Create\Header')->toHtml();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->_coreRegistry->registry('current_order')->getId()));
    }
}

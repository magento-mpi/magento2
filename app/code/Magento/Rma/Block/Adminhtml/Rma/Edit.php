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

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Variable to store RMA instance
     *
     * @var null|\Magento\Rma\Model\Rma
     */
    protected $_rma = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize RMA edit page. Set management buttons
     *
     */
    protected function _construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_controller  = 'adminhtml_rma';
        $this->_blockGroup  = 'Magento_Rma';

        parent::_construct();

        if (!$this->getRma()) {
            return;
        }
        $statusIsClosed = in_array(
            $this->getRma()->getStatus(),
            array(
                \Magento\Rma\Model\Rma\Source\Status::STATE_CLOSED,
                \Magento\Rma\Model\Rma\Source\Status::STATE_PROCESSED_CLOSED
            )
        );

        if (!$statusIsClosed) {
            $this->_addButton('save_and_edit_button', array(
                    'label'   => __('Save and Continue Edit'),
                    'class'   => 'save',
                    'data_attribute'  => array(
                        'mage-init' => array(
                            'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                        ),
                    ),
                ), 100
            );

            $this->_addButton('close', array(
                'label'     => __('Close'),
                'onclick'   => 'confirmSetLocation(\''
                                . __('Are you sure you want to close this returns request?')
                                .'\', \'' . $this->getCloseUrl().'\')'
                )
            );
        } else {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }

        $this->_addButton('print', array(
            'label'     => __('Print'),
            'class'     => 'save',
            'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
            ), 101
        );

        $this->_removeButton('delete');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $referer = $this->getRequest()->getServer('HTTP_REFERER');

        if (strpos($referer, 'sales_order') !== false) {
            return $this->getUrl('sales/order/view/',
                array(
                    'order_id'  => $this->getRma()->getOrderId(),
                    'active_tab'=> 'order_rma'
                )
            );
        } elseif (strpos($referer, 'customer') !== false) {
            return $this->getUrl('customer/index/edit/',
                array(
                    'id'  => $this->getRma()->getCustomerId(),
                    'active_tab'=> 'customer_edit_tab_rma'
                )
            );
        } else {
            return parent::getBackUrl();
        }
    }

    /**
     * Declare rma instance
     *
     * @return  \Magento\Rma\Model\Item
     */
    public function getRma()
    {
        if (is_null($this->_rma)) {
            $this->_rma = $this->_coreRegistry->registry('current_rma');
        }
        return $this->_rma;
    }

    /**
     * Get header text for RMA edit page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getRma()->getId()) {
            return __('Return #%1 - %2', intval($this->getRma()->getIncrementId()), $this->getRma()->getStatusLabel());
        }

        return '';
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('adminhtml/*/save', array(
            'rma_id' => $this->getRma()->getId()
        ));
    }

    /**
     * Get print RMA action URL
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('adminhtml/*/print', array(
            'rma_id' => $this->getRma()->getId()
        ));
    }

    /**
     * Get close RMA action URL
     *
     * @return string
     */
    public function getCloseUrl()
    {
        return $this->getUrl('adminhtml/*/close', array(
            'entity_id' => $this->getRma()->getId()
        ));
    }

}

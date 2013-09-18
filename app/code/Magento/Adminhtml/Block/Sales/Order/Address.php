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
 * Edit order address form container block
 */
namespace Magento\Adminhtml\Block\Sales\Order;

class Address extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_mode       = 'address';
        parent::_construct();
        $this->_updateButton('save', 'label', __('Save Order Address'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $address = $this->_coreRegistry->registry('order_address');
        $orderId = $address->getOrder()->getIncrementId();
        if ($address->getAddressType() == 'shipping') {
            $type = __('Shipping');
        } else {
            $type = __('Billing');
        }
        return __('Edit Order %1 %2 Address', $orderId, $type);
    }

    /**
     * Back button url getter
     *
     * @return string
     */
    public function getBackUrl()
    {
        $address = $this->_coreRegistry->registry('order_address');
        return $this->getUrl(
            '*/*/view',
            array('order_id' => $address ? $address->getOrder()->getId() : null)
        );
    }
}

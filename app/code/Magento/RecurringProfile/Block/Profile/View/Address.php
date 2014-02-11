<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Block\Profile\View;

/**
 * Recurring profile address view
 */
class Address extends \Magento\RecurringProfile\Block\Profile\View
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Sales\Model\Order\AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Sales\Model\Order\AddressFactory $addressFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $data);
        $this->_addressFactory = $addressFactory;
    }

    /**
     * Prepare address info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        if ('shipping' == $this->getAddressType()) {
            if ('1' == $this->_recurringProfile->getInfoValue('order_item_info', 'is_virtual')) {
                $this->getParentBlock()->unsetChild('sales.recurring.profile.view.shipping');
                return;
            }
            $key = 'shipping_address_info';
        } else {
            $key = 'billing_address_info';
        }
        $this->setIsAddress(true);
        $address = $this->_addressFactory->create(array('data' => $this->_recurringProfile->getData($key)));
        $this->_addInfo(array(
            'value' => preg_replace('/\\n{2,}/', "\n", $address->format('text')),
        ));
    }
}

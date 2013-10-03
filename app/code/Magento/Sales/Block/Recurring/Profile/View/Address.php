<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile\View;

/**
 * Recurring profile address view
 */
class Address extends \Magento\Sales\Block\Recurring\Profile\View
{

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Sales\Model\Order\AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Sales\Model\Order\AddressFactory $addressFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $storeManager, $locale, $coreData, $data);
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
            if ('1' == $this->_profile->getInfoValue('order_item_info', 'is_virtual')) {
                $this->getParentBlock()->unsetChild('sales.recurring.profile.view.shipping');
                return;
            }
            $key = 'shipping_address_info';
        } else {
            $key = 'billing_address_info';
        }
        $this->setIsAddress(true);
        $address = $this->_addressFactory->create(array('data' => $this->_profile->getData($key)));
        $this->_addInfo(array(
            'value' => preg_replace('/\\n{2,}/', "\n", $address->format('text')),
        ));
    }
}

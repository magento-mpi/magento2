<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create sidebar
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * @var \Magento\Customer\Model\FormFactory
     */
    protected $_customerFormFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Customer\Model\FormFactory $customerFormFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\FormFactory $customerFormFactory,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_customerFormFactory = $customerFormFactory;
        parent::__construct($context, $sessionQuote, $orderCreate, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_form');
    }

    /**
     * Retrieve url for loading blocks
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('sales/*/loadBlock');
    }

    /**
     * Retrieve url for form submiting
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('sales/*/save');
    }

    public function getCustomerSelectorDisplay()
    {
        $customerId = $this->getCustomerId();
        if (is_null($customerId)) {
            return 'block';
        }
        return 'none';
    }

    public function getStoreSelectorDisplay()
    {
        $storeId    = $this->getStoreId();
        $customerId = $this->getCustomerId();
        if (!is_null($customerId) && !$storeId) {
            return 'block';
        }
        return 'none';
    }

    public function getDataSelectorDisplay()
    {
        $storeId    = $this->getStoreId();
        $customerId = $this->getCustomerId();
        if (!is_null($customerId) && $storeId) {
            return 'block';
        }
        return 'none';
    }

    public function getOrderDataJson()
    {
        $data = array();
        if (!is_null($this->getCustomerId())) {
            $data['customer_id'] = $this->getCustomerId();
            $data['addresses'] = array();

            /* @var $addressForm \Magento\Customer\Model\Form */
            $addressForm = $this->_customerFormFactory->create()
                ->setFormCode('adminhtml_customer_address')
                ->setStore($this->getStore());
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $data['addresses'][$address->getId()] = $addressForm->setEntity($address)
                    ->outputData(\Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON);
            }
        }
        if (!is_null($this->getStoreId())) {
            $data['store_id'] = $this->getStoreId();
            $currency = $this->_locale->currency($this->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
            $data['shipping_method_reseted'] = !(bool)$this->getQuote()->getShippingAddress()->getShippingMethod();
            $data['payment_method'] = $this->getQuote()->getPayment()->getMethod();
        }
        return $this->_jsonEncoder->encode($data);
    }
}

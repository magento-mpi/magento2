<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Form
 * Adminhtml sales order create form block
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Customer form factory
     *
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_customerFormFactory;

    /**
     * Json encoder
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Address service
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressService;

    /**
     * Search criteria builder
     *
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * Filter builder
     *
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_customerFormFactory = $customerFormFactory;
        $this->addressService = $addressService;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_form');
    }

    /**
     * Retrieve url for loading blocks
     *
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('sales/*/loadBlock');
    }

    /**
     * Retrieve url for form submiting
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('sales/*/save');
    }

    /**
     * Get customer selector display
     *
     * @return string
     */
    public function getCustomerSelectorDisplay()
    {
        $customerId = $this->getCustomerId();
        if (is_null($customerId)) {
            return 'block';
        }
        return 'none';
    }

    /**
     * Get store selector display
     *
     * @return string
     */
    public function getStoreSelectorDisplay()
    {
        $storeId = $this->getStoreId();
        $customerId = $this->getCustomerId();
        if (!is_null($customerId) && !$storeId) {
            return 'block';
        }
        return 'none';
    }

    /**
     * Get data selector display
     *
     * @return string
     */
    public function getDataSelectorDisplay()
    {
        $storeId = $this->getStoreId();
        $customerId = $this->getCustomerId();
        if (!is_null($customerId) && $storeId) {
            return 'block';
        }
        return 'none';
    }

    /**
     * Get order data jason
     *
     * @return string
     */
    public function getOrderDataJson()
    {
        $data = [];
        if ($this->getCustomerId()) {
            $data['customer_id'] = $this->getCustomerId();
            $data['addresses'] = [];

            $this->criteriaBuilder->addFilter(
                ['eq' => $this->filterBuilder->setField('parent_id')->setValue($this->getCustomerId())->create()]
            );
            $criteria = $this->criteriaBuilder->create();
            $addresses = $this->addressService->getList($criteria)->getItems();

            foreach ($addresses as $addressModel) {
                $addressForm = $this->_customerFormFactory->create(
                    'customer_address',
                    'adminhtml_customer_address',
                    $addressModel->getData()
                );
                $data['addresses'][$addressModel->getId()] = $addressForm->outputData(
                    \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON
                );
            }
        }
        if (!is_null($this->getStoreId())) {
            $data['store_id'] = $this->getStoreId();
            $currency = $this->_localeCurrency->getCurrency($this->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
            $data['shipping_method_reseted'] = !(bool)$this->getQuote()->getShippingAddress()->getShippingMethod();
            $data['payment_method'] = $this->getQuote()->getPayment()->getMethod();
        }

        return $this->_jsonEncoder->encode($data);
    }
}

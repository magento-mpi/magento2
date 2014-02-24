<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

use Magento\Customer\Service\V1\CustomerServiceInterface as CustomerService;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface as CustomerAddressService;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Exception\NoSuchEntityException;

/**
 * One page common functionality block
 */
abstract class AbstractOnepage extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\App\Cache\Type\Config
     */
    protected $_configCacheType;

    protected $_customer;
    protected $_quote;
    protected $_countryCollection;
    protected $_regionCollection;
    protected $_addressesCollection;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Directory\Model\Resource\Region\CollectionFactory
     */
    protected $_regionCollectionFactory;

    /**
     * @var \Magento\Directory\Model\Resource\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var CustomerService
     */
    protected $_customerService;

    /**
     * @var CustomerAddressService
     */
    protected $_customerAddressService;
    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    private $_addressConfig;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $resourceSession
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param CustomerService $customerService
     * @param CustomerAddressService $customerAddressService
     * @param AddressConfig $addressConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $resourceSession,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        CustomerService $customerService,
        CustomerAddressService $customerAddressService,
        AddressConfig $addressConfig,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_configCacheType = $configCacheType;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $resourceSession;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_regionCollectionFactory = $regionCollectionFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_customerService = $customerService;
        $this->_customerAddressService = $customerAddressService;
        $this->_addressConfig = $addressConfig;
    }

    /**
     * Get config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_storeConfig->getConfig($path);
    }

    /**
     * Get logged in customer
     *
     * @return \Magento\Customer\Service\V1\Dto\Customer
     */
    protected function _getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = $this->_customerService->getCustomer($this->_customerSession->getCustomerId());
        }
        return $this->_customer;
    }

    /**
     * Retrieve checkout session model
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Retrieve sales quote model
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = $this->_countryCollectionFactory->create()->loadByStore();
        }
        return $this->_countryCollection;
    }

    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = $this->_regionCollectionFactory->create()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    public function customerHasAddresses()
    {
        try {
            return count($this->_customerAddressService->getAddresses($this->_getCustomer()->getCustomerId()));
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $customerId = $this->_getCustomer()->getCustomerId();
            $options = [];

            try {
                $addresses = $this->_customerAddressService->getAddresses($customerId);
            } catch (NoSuchEntityException $e) {
                $addresses = [];
            }

            foreach ($addresses as $address) {
                /** @var \Magento\Customer\Service\V1\Dto\Address $address */
                $label = $this->_addressConfig
                    ->getFormatByCode(AddressConfig::DEFAULT_ADDRESS_FORMAT)
                    ->getRenderer()
                    ->renderArray($address->__toArray());

                $options[] = [
                    'value' => $address->getId(),
                    'label' => $label
                ];
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                try {
                    if ($type == 'billing') {
                        $address = $this->_customerAddressService->getDefaultBillingAddress($customerId);
                    } else {
                        $address = $this->_customerAddressService->getDefaultShippingAddress($customerId);
                    }

                    $addressId = $address->getId();
                } catch (NoSuchEntityException $e) {
                    // Do nothing
                }
            }

            $select = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
                ->setName($type . '_address_id')
                ->setId($type . '-address-select')
                ->setClass('address-select')
                //->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                // temp disable inline javascript, need to clean this later
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', __('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = $this->_coreData->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        return $select->getHtml();
    }


    public function getRegionHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setName($type.'[region]')
            ->setId($type.':region')
            ->setTitle(__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }

    public function getCountryOptions()
    {
        $options = false;
        $cacheId = 'DIRECTORY_COUNTRY_SELECT_STORE_' . $this->_storeManager->getStore()->getCode();
        if ($optionsCache = $this->_configCacheType->load($cacheId)) {
            $options = unserialize($optionsCache);
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheId);
        }
        return $options;
    }

    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        return array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return true;
    }
}

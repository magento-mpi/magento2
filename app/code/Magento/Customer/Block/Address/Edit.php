<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Address;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\Data\Address;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Exception\NoSuchEntityException;

/**
 * Customer address edit block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Directory\Block\Data
{
    /**
     * @var Address|null
     */
    protected $_address = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface
     */
    protected $_addressService;

    /**
     * @var \Magento\Customer\Service\V1\Data\AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentServiceInterface
     */
    protected $customerCurrentService;

    /**
     * Constructor
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder
     * @param \Magento\Customer\Service\V1\CustomerCurrentServiceInterface $customerCurrentService
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\CustomerCurrentServiceInterface $customerCurrentService,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_addressService = $addressService;
        $this->_addressBuilder = $addressBuilder;
        $this->customerCurrentService = $customerCurrentService;
        parent::__construct(
            $context,
            $coreData,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Prepare the layout of the address edit block.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        // Init address object
        if ($addressId = $this->getRequest()->getParam('id')) {
            try {
                $this->_address = $this->_addressService->getAddress($addressId);
            } catch (NoSuchEntityException $e) {
            }
        }

        if (is_null($this->_address) || !$this->_address->getId()) {
            $this->_address = $this->_addressBuilder->setPrefix(
                $this->getCustomer()->getPrefix()
            )->setFirstname(
                $this->getCustomer()->getFirstname()
            )->setMiddlename(
                $this->getCustomer()->getMiddlename()
            )->setLastname(
                $this->getCustomer()->getLastname()
            )->setSuffix(
                $this->getCustomer()->getSuffix()
            )->create();
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }

        if ($postedData = $this->_customerSession->getAddressFormData(true)) {
            if (!empty($postedData['region_id']) || !empty($postedData['region'])) {
                $postedData['region'] = array(
                    'region_id' => $postedData['region_id'],
                    'region' => $postedData['region']
                );
            }
            $this->_address = $this->_addressBuilder->mergeDataObjectWithArray($this->_address, $postedData);
        }

        return $this;
    }

    /**
     * Generate name block html.
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()->createBlock(
            'Magento\Customer\Block\Widget\Name'
        )->setObject(
            $this->getAddress()
        );

        return $nameBlock->toHtml();
    }

    /**
     * Return the title, either editing an existing address, or adding a new one.
     *
     * @return string
     */
    public function getTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getAddress()->getId()) {
            $title = __('Edit Address');
        } else {
            $title = __('Add New Address');
        }
        return $title;
    }

    /**
     * Return the Url to go back.
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getData('back_url')) {
            return $this->getData('back_url');
        }

        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * Return the Url for saving.
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'customer/address/formPost',
            array('_secure' => true, 'id' => $this->getAddress()->getId())
        );
    }

    /**
     * Return the associated address.
     *
     * @return Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * Return the specified numbered street line.
     *
     * @param int $lineNumber
     * @return string
     */
    public function getStreetLine($lineNumber)
    {
        $street = $this->_address->getStreet();
        return isset($street[$lineNumber - 1]) ? $street[$lineNumber - 1] : '';
    }

    /**
     * Return the country Id.
     *
     * @return int|null|string
     */
    public function getCountryId()
    {
        if ($countryId = $this->getAddress()->getCountryId()) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Return the name of the region for the address being edited.
     *
     * @return string region name
     */
    public function getRegion()
    {
        $region = $this->getAddress()->getRegion();
        return is_null($region) ? '' : $region->getRegion();
    }

    /**
     * Return the id of the region being edited.
     *
     * @return int region id
     */
    public function getRegionId()
    {
        $region = $this->getAddress()->getRegion();
        return is_null($region) ? 0 : $region->getRegionId();
    }

    /**
     * Retrieve the number of addresses associated with the customer given a customer Id.
     *
     * @return int
     */
    public function getCustomerAddressCount()
    {
        return count($this->_addressService->getAddresses($this->_customerSession->getCustomerId()));
    }

    /**
     * Determine if the address can be set as the default billing address.
     *
     * @return bool|int
     */
    public function canSetAsDefaultBilling()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultBilling();
    }

    /**
     * Determine if the address can be set as the default shipping address.
     *
     * @return bool|int
     */
    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultShipping();
    }

    /**
     * Is the address the default billing address?
     *
     * @return bool
     */
    public function isDefaultBilling()
    {
        return (bool)$this->getAddress()->isDefaultBilling();
    }

    /**
     * Is the address the default shipping address?
     *
     * @return bool
     */
    public function isDefaultShipping()
    {
        return (bool)$this->getAddress()->isDefaultShipping();
    }

    /**
     * Retrieve the Customer Data using the customer Id from the customer session.
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customerCurrentService->getCustomer();
    }

    /**
     * Return back button Url, either to customer address or account.
     *
     * @return string
     */
    public function getBackButtonUrl()
    {
        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * Get config value.
     *
     * @param string $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}

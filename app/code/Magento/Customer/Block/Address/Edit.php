<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address edit block
 */
namespace Magento\Customer\Block\Address;

use Magento\Customer\Service\V1\Dto\Address;
use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Exception\NoSuchEntityException;

class Edit extends \Magento\Directory\Block\Data
{
    /**
     * @var Address
     */
    protected $_address = null;
    protected $_countryCollection;
    protected $_regionCollection;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface
     */
    protected $_addressService;

    /**
     * @var \Magento\Customer\Service\V1\Dto\AddressBuilder
     */
    protected $_addressBuilder;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentServiceInterface
     */
    protected $customerCurrentService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressCurrentServiceInterface
     */
    protected $customerAddressCurrentService;


    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\App\ConfigInterface $config,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Service\V1\Dto\AddressBuilder $addressBuilder,
        \Magento\Customer\Service\V1\CustomerCurrentServiceInterface $customerCurrentService,
        array $data = array()
    ) {
        $this->_config                  = $config;
        $this->_customerSession         = $customerSession;
        $this->_addressService          = $addressService;
        $this->_addressBuilder          = $addressBuilder;
        $this->customerCurrentService   = $customerCurrentService;
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

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        // Init address object
        if ($addressId = $this->getRequest()->getParam('id')) {
            try {
                $this->_address = $this->_addressService->getAddressById($addressId);
            } catch (NoSuchEntityException $e) {
                // something went wrong, but we are ignore it for now
            }
        }

        if (is_null($this->_address) || !$this->_address->getId()) {
            $this->_address =
                $this->_addressBuilder
                    ->setPrefix($this->getCustomer()->getPrefix())
                    ->setFirstname($this->getCustomer()->getFirstname())
                    ->setMiddlename($this->getCustomer()->getMiddlename())
                    ->setLastname($this->getCustomer()->getLastname())
                    ->setSuffix($this->getCustomer()->getSuffix())
                    ->create();
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }

        if ($postedData = $this->_customerSession->getAddressFormData(true)) {
            if (!empty($postedData['region_id']) || !empty($postedData['region'])) {
                $postedData['region'] = [
                    'region_id' => $postedData['region_id'],
                    'region' => $postedData['region'],
                ];
            }
            $this->_address = $this->_addressBuilder
                ->populateWithArray(array_merge($this->_address->__toArray(), $postedData))
                ->create();
        }
    }

    /**
     * Generate name block html
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock('Magento\Customer\Block\Widget\Name')
            ->setObject($this->getAddress());

        return $nameBlock->toHtml();
    }

    public function getTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getAddress()->getId()) {
            $title = __('Edit Address');
        }
        else {
            $title = __('Add New Address');
        }
        return $title;
    }

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

    public function getSaveUrl()
    {
        return $this->_urlBuilder->getUrl(
            'customer/address/formPost',
            array('_secure'=>true, 'id'=>$this->getAddress()->getId())
        );
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @param int $lineNumber
     * @return string
     */
    public function getStreetLine($lineNumber)
    {
        $street = $this->_address->getStreet();
        return isset($street[$lineNumber-1]) ? $street[$lineNumber-1] : '';
    }

    public function getCountryId()
    {
        if ($countryId = $this->getAddress()->getCountryId()) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Return the name of the region for the address being edited
     *
     * @return string region name
     */
    public function getRegion()
    {
        $region = $this->getAddress()->getRegion();
        return is_null($region) ? '' : $region->getRegion();
    }

    /**
     * Return the id of the region being edited
     *
     * @return int region id
     */
    public function getRegionId()
    {
        $region = $this->getAddress()->getRegion();
        return is_null($region) ? 0 : $region->getRegionId();
    }

    public function getCustomerAddressCount()
    {
        return count($this->_customerSession->getCustomer()->getAddresses());
    }

    public function canSetAsDefaultBilling()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultBilling();
    }

    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultShipping();
    }

    public function isDefaultBilling()
    {
        return (bool)$this->getAddress()->isDefaultBilling();
    }

    public function isDefaultShipping()
    {
        return (bool)$this->getAddress()->isDefaultShipping();
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customerCurrentService->getCustomer();
    }

    public function getBackButtonUrl()
    {
        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
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
}

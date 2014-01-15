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

class Edit extends \Magento\Directory\Block\Data
{
    /**
     * @var Address
     */
    protected $_address;
    protected $_countryCollection;
    protected $_regionCollection;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface
     */
    protected $_addressService;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerServiceInterface $customerService
     * @param \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory,
        \Magento\Core\Model\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerServiceInterface $customerService,
        \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService,
        array $data = array()
    ) {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_customerService = $customerService;
        parent::__construct(
            $context, $coreData, $jsonEncoder, $configCacheType, $regionCollFactory, $countryCollFactory, $data
        );
        $this->_addressService = $addressService;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        // Init address object
        if ($addressId = $this->getRequest()->getParam('id')) {
            $customerId = $this->_customerSession->getCustomerId();
            try {
                $this->_address = $this->_addressService->getAddressById($customerId, $addressId);
            } catch (\Magento\Customer\Service\Entity\V1\Exception $e) {
                // something went wrong, but we are ignore it for now
                $this->_address = $this->_createAddress();
            }
        } else {
            $this->_address = $this->_createAddress();
        }

        if (!$this->_address->getId()) {
            $this->_address->setPrefix($this->getCustomer()->getPrefix())
                ->setFirstname($this->getCustomer()->getFirstname())
                ->setMiddlename($this->getCustomer()->getMiddlename())
                ->setLastname($this->getCustomer()->getLastname())
                ->setSuffix($this->getCustomer()->getSuffix());
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }

        if ($postedData = $this->_customerSession->getAddressFormData(true)) {
            foreach ($postedData as $key => $value) {
                $this->_address->setAttribute($key, $value);
            }
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

    public function getCountryId()
    {
        if ($countryId = $this->getAddress()->getCountryId()) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    public function getRegionId()
    {
        return $this->getAddress()->getRegion()->getRegionId();
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
        $defaultBilling = $this->_addressService
            ->getDefaultBillingAddress($this->_customerSession->getCustomerId());
        return $this->getAddress()->getId() && $this->getAddress()->getId() == $defaultBilling;
    }

    public function isDefaultShipping()
    {
        $defaultShipping = $this->_addressService
            ->getDefaultShippingAddress($this->_customerSession->getCustomerId());
        return $this->getAddress()->getId() && $this->getAddress()->getId() == $defaultShipping;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customerService->getCustomer($this->_customerSession->getId());
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

    /**
     * @return Address
     */
    protected function _createAddress()
    {
        return new Address();
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Address;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * Customer address book block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Book extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;

    /**
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var CustomerAddressServiceInterface
     */
    protected $_addressService;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param CustomerAddressServiceInterface $addressService
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        CustomerAccountServiceInterface $customerAccountService,
        CustomerAddressServiceInterface $addressService,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        \Magento\Customer\Model\Address\Config $addressConfig,
        array $data = array()
    ) {
        $this->_customerAccountService = $customerAccountService;
        $this->currentCustomer = $currentCustomer;
        $this->_addressService = $addressService;
        $this->_addressConfig = $addressConfig;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')
            ->setTitle(__('Address Book'));

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getAddAddressUrl()
    {
        return $this->getUrl('customer/address/new', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/', array('_secure'=>true));
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('customer/address/delete');
    }

    /**
     * @param int $addressId
     * @return string
     */
    public function getAddressEditUrl($addressId)
    {
        return $this->getUrl('customer/address/edit', array('_secure'=>true, 'id' => $addressId));
    }

    /**
     * @return bool
     */
    public function hasPrimaryAddress()
    {
        return $this->getDefaultBilling() || $this->getDefaultShipping();
    }

    /**
     * @return \Magento\Customer\Service\V1\Data\Address[]|bool
     */
    public function getAdditionalAddresses()
    {
        try {
            $addresses = $this->_addressService->getAddresses($this->currentCustomer->getCustomerId());
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            return false;
        }
        $primaryAddressIds = [$this->getDefaultBilling(), $this->getDefaultShipping()];
        foreach ($addresses as $address) {
            if (!in_array($address->getId(), $primaryAddressIds)) {
                $additional[] = $address;
            }
        }
        return empty($additional) ? false : $additional;
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return string
     */
    public function getAddressHtml(\Magento\Customer\Service\V1\Data\Address $address = null)
    {
        if (!is_null($address)) {
            /** @var \Magento\Customer\Block\Address\Renderer\RendererInterface $renderer */
            $renderer = $this->_addressConfig->getFormatByCode('html')->getRenderer();
            return $renderer->renderArray(\Magento\Customer\Service\V1\Data\AddressConverter::toFlatArray($address));
        }
        return '';
    }

    /**
     * @return \Magento\Customer\Service\V1\Data\Customer|null
     */
    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if (is_null($customer)) {
            try {
                $customer = $this->_customerAccountService->getCustomer($this->currentCustomer->getCustomerId());
            } catch (\Magento\Exception\NoSuchEntityException $e) {
                return null;
            }
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * @return int|null
     */
    public function getDefaultBilling()
    {
        $customer = $this->getCustomer();
        if (is_null($customer)) {
            return null;
        } else {
            return $customer->getDefaultBilling();
        }
    }

    /**
     * @param int $addressId
     * @return \Magento\Customer\Service\V1\Data\Address|null
     */
    public function getAddressById($addressId)
    {
        try {
            return $this->_addressService->getAddress($addressId);
        } catch (\Magento\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return int|null
     */
    public function getDefaultShipping()
    {
        $customer = $this->getCustomer();
        if (is_null($customer)) {
            return null;
        } else {
            return $customer->getDefaultShipping();
        }
    }
}

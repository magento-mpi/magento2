<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Block\Checkout\Address;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Helper\Address as CustomerAddressHelper;

/**
 * Class Select
 * Multishipping checkout select billing address
 */
class Select extends \Magento\Multishipping\Block\Checkout\AbstractMultishipping
{
    /**
     * @var CustomerAddressHelper
     */
    protected $_customerAddressHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param CustomerAddressHelper $customerAddressHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        CustomerAddressHelper $customerAddressHelper,
        array $data = []
    ) {
        $this->_customerAddressHelper = $customerAddressHelper;
        parent::__construct($context, $multishipping, $data);
    }

    /**
     * @var bool
     */
    protected $_isScopePrivate = true;

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->setTitle(__('Change Billing Address') . ' - ' . $this->pageConfig->getDefaultTitle());
        return parent::_prepareLayout();
    }

    /**
     * Get a list of current customer addresses.
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     */
    public function getAddress()
    {
        $addresses = $this->getData('address_collection');
        if (is_null($addresses)) {
            try {
                $addresses = $this->_multishipping->getCustomer()->getAddresses();
            } catch (NoSuchEntityException $e) {
                return [];
            }
            $this->setData('address_collection', $addresses);
        }
        return $addresses;
    }

    /**
     * Represent customer address in HTML format.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getAddressAsHtml(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        $formatTypeRenderer = $this->_customerAddressHelper->getFormatTypeRenderer('html');
        $result = '';
        if ($formatTypeRenderer) {
            $arrayData = \Magento\Framework\Api\ExtensibleDataObjectConverter::toFlatArray($address);
            $result = $formatTypeRenderer->renderArray($arrayData);
        }
        return $result;
    }

    /**
     * Check if provided address is default customer billing address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return bool
     */
    public function isAddressDefaultBilling(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        return $address->getId() == $this->_multishipping->getCustomer()->getDefaultBilling()->getId();
    }

    /**
     * Check if provided address is default customer shipping address.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return bool
     */
    public function isAddressDefaultShipping(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        return $address->getId() == $this->_multishipping->getCustomer()->getDefaultShipping()->getId();
    }

    /**
     * Get URL of customer address edit page.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getEditAddressUrl(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        return $this->getUrl('*/*/editAddress', ['id' => $address->getId()]);
    }

    /**
     * Get URL of page, at which customer billing address can be set.
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getSetAddressUrl(\Magento\Customer\Api\Data\AddressInterface $address)
    {
        return $this->getUrl('*/*/setBilling', ['id' => $address->getId()]);
    }

    /**
     * @return string
     */
    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/newBilling');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/checkout/billing');
    }
}

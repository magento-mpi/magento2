<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Block\Checkout\Address;

use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Exception\NoSuchEntityException;

/**
 * Multishipping checkout select billing address
 */
class Select extends \Magento\Multishipping\Block\Checkout\AbstractMultishipping
{
    /**
     * @var CustomerAddressServiceInterface
     */
    protected $_customerAddressService;

    /**
     * @var CustomerAddressHelper
     */
    protected $_customerAddressHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping
     * @param CustomerAddressServiceInterface $customerAddressService
     * @param CustomerAddressHelper $customerAddressHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        CustomerAddressServiceInterface $customerAddressService,
        CustomerAddressHelper $customerAddressHelper,
        array $data = array()
    ) {
        $this->_customerAddressService = $customerAddressService;
        $this->_customerAddressHelper = $customerAddressHelper;
        parent::__construct($context, $multishipping, $data);
    }

    /**
     * @var bool
     */
    protected $_isScopePrivate = true;

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Change Billing Address') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * Get a list of current customer addresses.
     *
     * @return \Magento\Customer\Service\V1\Data\Address[]
     */
    public function getAddressCollection()
    {
        $addresses = $this->getData('address_collection');
        if (is_null($addresses)) {
            try{
                $addresses = $this->_customerAddressService->getAddresses(
                    $this->_multishipping->getCustomer()->getCustomerId()
                );
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
     * @param \Magento\Customer\Service\V1\Data\Address $addressData
     * @return string
     */
    public function getAddressAsHtml($addressData)
    {
        $formatTypeRenderer = $this->_customerAddressHelper->getFormatTypeRenderer('html');
        $result = '';
        if ($formatTypeRenderer) {
            $result = $formatTypeRenderer->renderArray(
                \Magento\Convert\ConvertArray::toFlatArray($addressData->__toArray())
            );
        }
        return $result;
    }

    /**
     * Check if provided address is default customer billing address.
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return bool
     */
    public function isAddressDefaultBilling($address)
    {
        return $address->getId() == $this->_multishipping->getCustomer()->getDefaultBilling();
    }

    /**
     * Check if provided address is default customer shipping address.
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return bool
     */
    public function isAddressDefaultShipping($address)
    {
        return $address->getId() == $this->_multishipping->getCustomer()->getDefaultShipping();
    }

    /**
     * Get URL of customer address edit page.
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return string
     */
    public function getEditAddressUrl($address)
    {
        return $this->getUrl('*/*/editAddress', array('id' => $address->getId()));
    }

    /**
     * Get URL of page, at which customer billing address can be set.
     *
     * @param \Magento\Customer\Service\V1\Data\Address $address
     * @return string
     */
    public function getSetAddressUrl($address)
    {
        return $this->getUrl('*/*/setBilling', array('id' => $address->getId()));
    }

    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/newBilling');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/checkout/billing');
    }
}

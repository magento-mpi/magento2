<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Customer\Address;

use Magento\Customer\Model\Context;

/**
 * GiftRegistry shipping Address block
 */
class Edit extends \Magento\GiftRegistry\Block\Customer\Edit\AbstractEdit
{
    /**
     * Contains logged in customer
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $coreData,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $registry,
            $customerSession,
            $attributeConfig,
            $data
        );
        $this->httpContext = $httpContext;
    }

    /**
     * Getter for entity object
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return $this->_registry->registry('magento_giftregistry_entity');
    }

    /**
     * Getter for address object
     *
     * @return \Magento\Customer\Model\Address
     */
    public function getAddress()
    {
        return $this->_registry->registry('magento_giftregistry_address');
    }

    /**
     * Check customer has address
     *
     * @return bool
     */
    public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses());
    }

    /**
     * Return html select input element for Address (None/<address1>/<address2>/New/)
     *
     * @param string $domId
     * @return string
     */
    public function getAddressHtmlSelect($domId = 'address_type_or_id')
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array(array('value' => \Magento\GiftRegistry\Helper\Data::ADDRESS_NONE, 'label' => __('None')));
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array('value' => $address->getId(), 'label' => $address->format('oneline'));
            }
            $options[] = array(
                'value' => \Magento\GiftRegistry\Helper\Data::ADDRESS_NEW,
                'label' => __('New Address')
            );

            $select = $this->getLayout()->createBlock(
                'Magento\Framework\View\Element\Html\Select'
            )->setName(
                'address_type_or_id'
            )->setId(
                $domId
            )->setClass(
                'address-select'
            )->setOptions(
                $options
            );

            return $select->getHtml();
        }
        return '';
    }

    /**
     * Get logged in customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = $this->customerSession->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}

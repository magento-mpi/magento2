<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order;

/**
 * Sales order address model
 *
 * @method \Magento\Sales\Model\Resource\Order\Address _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Address getResource()
 * @method int getParentId()
 * @method Address setParentId(int $value)
 * @method int getCustomerAddressId()
 * @method Address setCustomerAddressId(int $value)
 * @method \Magento\Customer\Api\Data\AddressInterface getCustomerAddress()
 * @method Address setCustomerAddressData(\Magento\Customer\Api\Data\AddressInterface $value)
 * @method int getQuoteAddressId()
 * @method Address setQuoteAddressId(int $value)
 * @method Address setRegionId(int $value)
 * @method int getCustomerId()
 * @method Address setCustomerId(int $value)
 * @method string getFax()
 * @method Address setFax(string $value)
 * @method Address setRegion(string $value)
 * @method string getPostcode()
 * @method Address setPostcode(string $value)
 * @method string getLastname()
 * @method Address setLastname(string $value)
 * @method string getCity()
 * @method Address setCity(string $value)
 * @method string getEmail()
 * @method Address setEmail(string $value)
 * @method string getTelephone()
 * @method Address setTelephone(string $value)
 * @method string getCountryId()
 * @method Address setCountryId(string $value)
 * @method string getFirstname()
 * @method Address setFirstname(string $value)
 * @method string getAddressType()
 * @method Address setAddressType(string $value)
 * @method string getPrefix()
 * @method Address setPrefix(string $value)
 * @method string getMiddlename()
 * @method Address setMiddlename(string $value)
 * @method string getSuffix()
 * @method Address setSuffix(string $value)
 * @method string getCompany()
 * @method Address setCompany(string $value)
 */
class Address extends \Magento\Customer\Model\Address\AbstractAddress
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_order_address';

    /**
     * @var string
     */
    protected $_eventObject = 'address';

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $directoryData,
            $eavConfig,
            $addressConfig,
            $regionFactory,
            $countryFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Address');
    }

    /**
     * Set order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->_orderFactory->create()->load($this->getParentId());
        }
        return $this->_order;
    }

    /**
     * Before object save manipulations
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        // Init customer address id if customer address is assigned
        $customerData = $this->getCustomerAddressData();
        if ($customerData) {
            $this->setCustomerAddressId($customerData->getId());
        }

        return $this;
    }
}

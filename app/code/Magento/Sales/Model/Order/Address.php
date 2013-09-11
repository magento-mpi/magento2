<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales order address model
 *
 * @method \Magento\Sales\Model\Resource\Order\Address _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Address getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Address setParentId(int $value)
 * @method int getCustomerAddressId()
 * @method \Magento\Sales\Model\Order\Address setCustomerAddressId(int $value)
 * @method int getQuoteAddressId()
 * @method \Magento\Sales\Model\Order\Address setQuoteAddressId(int $value)
 * @method \Magento\Sales\Model\Order\Address setRegionId(int $value)
 * @method int getCustomerId()
 * @method \Magento\Sales\Model\Order\Address setCustomerId(int $value)
 * @method string getFax()
 * @method \Magento\Sales\Model\Order\Address setFax(string $value)
 * @method \Magento\Sales\Model\Order\Address setRegion(string $value)
 * @method string getPostcode()
 * @method \Magento\Sales\Model\Order\Address setPostcode(string $value)
 * @method string getLastname()
 * @method \Magento\Sales\Model\Order\Address setLastname(string $value)
 * @method string getCity()
 * @method \Magento\Sales\Model\Order\Address setCity(string $value)
 * @method string getEmail()
 * @method \Magento\Sales\Model\Order\Address setEmail(string $value)
 * @method string getTelephone()
 * @method \Magento\Sales\Model\Order\Address setTelephone(string $value)
 * @method string getCountryId()
 * @method \Magento\Sales\Model\Order\Address setCountryId(string $value)
 * @method string getFirstname()
 * @method \Magento\Sales\Model\Order\Address setFirstname(string $value)
 * @method string getAddressType()
 * @method \Magento\Sales\Model\Order\Address setAddressType(string $value)
 * @method string getPrefix()
 * @method \Magento\Sales\Model\Order\Address setPrefix(string $value)
 * @method string getMiddlename()
 * @method \Magento\Sales\Model\Order\Address setMiddlename(string $value)
 * @method string getSuffix()
 * @method \Magento\Sales\Model\Order\Address setSuffix(string $value)
 * @method string getCompany()
 * @method \Magento\Sales\Model\Order\Address setCompany(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order;

class Address extends \Magento\Customer\Model\Address\AbstractAddress
{
    protected $_order;

    protected $_eventPrefix = 'sales_order_address';
    protected $_eventObject = 'address';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Resource\Order\Address');
    }

    /**
     * Set order
     *
     * @return \Magento\Sales\Model\Order\Address
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
            $this->_order = \Mage::getModel('\Magento\Sales\Model\Order')->load($this->getParentId());
        }
        return $this->_order;
    }

    /**
     * Before object save manipulations
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        // Init customer address id if customer address is assigned
        if ($this->getCustomerAddress()) {
            $this->setCustomerAddressId($this->getCustomerAddress()->getId());
        }

        return $this;
    }
}

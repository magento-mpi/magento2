<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales order address model
 *
 * @method Mage_Sales_Model_Resource_Order_Address _getResource()
 * @method Mage_Sales_Model_Resource_Order_Address getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Address setParentId(int $value)
 * @method int getCustomerAddressId()
 * @method Mage_Sales_Model_Order_Address setCustomerAddressId(int $value)
 * @method int getQuoteAddressId()
 * @method Mage_Sales_Model_Order_Address setQuoteAddressId(int $value)
 * @method Mage_Sales_Model_Order_Address setRegionId(int $value)
 * @method int getCustomerId()
 * @method Mage_Sales_Model_Order_Address setCustomerId(int $value)
 * @method string getFax()
 * @method Mage_Sales_Model_Order_Address setFax(string $value)
 * @method Mage_Sales_Model_Order_Address setRegion(string $value)
 * @method string getPostcode()
 * @method Mage_Sales_Model_Order_Address setPostcode(string $value)
 * @method string getLastname()
 * @method Mage_Sales_Model_Order_Address setLastname(string $value)
 * @method string getCity()
 * @method Mage_Sales_Model_Order_Address setCity(string $value)
 * @method string getEmail()
 * @method Mage_Sales_Model_Order_Address setEmail(string $value)
 * @method string getTelephone()
 * @method Mage_Sales_Model_Order_Address setTelephone(string $value)
 * @method string getCountryId()
 * @method Mage_Sales_Model_Order_Address setCountryId(string $value)
 * @method string getFirstname()
 * @method Mage_Sales_Model_Order_Address setFirstname(string $value)
 * @method string getAddressType()
 * @method Mage_Sales_Model_Order_Address setAddressType(string $value)
 * @method string getPrefix()
 * @method Mage_Sales_Model_Order_Address setPrefix(string $value)
 * @method string getMiddlename()
 * @method Mage_Sales_Model_Order_Address setMiddlename(string $value)
 * @method string getSuffix()
 * @method Mage_Sales_Model_Order_Address setSuffix(string $value)
 * @method string getCompany()
 * @method Mage_Sales_Model_Order_Address setCompany(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Address extends Magento_Customer_Model_Address_Abstract
{
    protected $_order;

    protected $_eventPrefix = 'sales_order_address';
    protected $_eventObject = 'address';

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Resource_Order_Address');
    }

    /**
     * Set order
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = Mage::getModel('Mage_Sales_Model_Order')->load($this->getParentId());
        }
        return $this->_order;
    }

    /**
     * Before object save manipulations
     *
     * @return Mage_Sales_Model_Order_Address
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

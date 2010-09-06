<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_Sales_Model_Resource_Order_Address _getResource()
 * @method Mage_Sales_Model_Resource_Order_Address getResource()
 * @method Mage_Sales_Model_Order_Address getParentId()
 * @method int setParentId(int $value)
 * @method Mage_Sales_Model_Order_Address getCustomerAddressId()
 * @method int setCustomerAddressId(int $value)
 * @method Mage_Sales_Model_Order_Address getQuoteAddressId()
 * @method int setQuoteAddressId(int $value)
 * @method int setRegionId(int $value)
 * @method Mage_Sales_Model_Order_Address getCustomerId()
 * @method int setCustomerId(int $value)
 * @method Mage_Sales_Model_Order_Address getFax()
 * @method string setFax(string $value)
 * @method string setRegion(string $value)
 * @method Mage_Sales_Model_Order_Address getPostcode()
 * @method string setPostcode(string $value)
 * @method Mage_Sales_Model_Order_Address getLastname()
 * @method string setLastname(string $value)
 * @method Mage_Sales_Model_Order_Address getCity()
 * @method string setCity(string $value)
 * @method Mage_Sales_Model_Order_Address getEmail()
 * @method string setEmail(string $value)
 * @method Mage_Sales_Model_Order_Address getTelephone()
 * @method string setTelephone(string $value)
 * @method Mage_Sales_Model_Order_Address getCountryId()
 * @method string setCountryId(string $value)
 * @method Mage_Sales_Model_Order_Address getFirstname()
 * @method string setFirstname(string $value)
 * @method Mage_Sales_Model_Order_Address getAddressType()
 * @method string setAddressType(string $value)
 * @method Mage_Sales_Model_Order_Address getPrefix()
 * @method string setPrefix(string $value)
 * @method Mage_Sales_Model_Order_Address getMiddlename()
 * @method string setMiddlename(string $value)
 * @method Mage_Sales_Model_Order_Address getSuffix()
 * @method string setSuffix(string $value)
 * @method Mage_Sales_Model_Order_Address getCompany()
 * @method string setCompany(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Address extends Mage_Customer_Model_Address_Abstract
{
    protected $_order;

    protected $_eventPrefix = 'sales_order_address';
    protected $_eventObject = 'address';

    protected function _construct()
    {
        $this->_init('sales/order_address');
    }

    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    public function getOrder()
    {
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

        return $this;
    }
}

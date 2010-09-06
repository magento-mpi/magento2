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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce resource model
 *
 * @method Mage_Oscommerce_Model_Resource_Oscommerce_Order _getResource()
 * @method Mage_Oscommerce_Model_Resource_Oscommerce_Order getResource()
 * @method int getOrdersId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setOrdersId(int $value)
 * @method int getCustomersId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersId(int $value)
 * @method int getMagentoCustomersId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setMagentoCustomersId(int $value)
 * @method int getImportId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setImportId(int $value)
 * @method int getWebsiteId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setWebsiteId(int $value)
 * @method string getCustomersName()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersName(string $value)
 * @method string getCustomersCompany()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersCompany(string $value)
 * @method string getCustomersStreetAddress()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersStreetAddress(string $value)
 * @method string getCustomersSuburb()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersSuburb(string $value)
 * @method string getCustomersCity()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersCity(string $value)
 * @method string getCustomersPostcode()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersPostcode(string $value)
 * @method string getCustomersState()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersState(string $value)
 * @method string getCustomersCountry()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersCountry(string $value)
 * @method string getCustomersTelephone()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersTelephone(string $value)
 * @method string getCustomersEmailAddress()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersEmailAddress(string $value)
 * @method int getCustomersAddressFormatId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCustomersAddressFormatId(int $value)
 * @method string getDeliveryName()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryName(string $value)
 * @method string getDeliveryCompany()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryCompany(string $value)
 * @method string getDeliveryStreetAddress()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryStreetAddress(string $value)
 * @method string getDeliverySuburb()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliverySuburb(string $value)
 * @method string getDeliveryCity()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryCity(string $value)
 * @method string getDeliveryPostcode()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryPostcode(string $value)
 * @method string getDeliveryState()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryState(string $value)
 * @method string getDeliveryCountry()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryCountry(string $value)
 * @method int getDeliveryAddressFormatId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDeliveryAddressFormatId(int $value)
 * @method string getBillingName()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingName(string $value)
 * @method string getBillingCompany()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingCompany(string $value)
 * @method string getBillingStreetAddress()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingStreetAddress(string $value)
 * @method string getBillingSuburb()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingSuburb(string $value)
 * @method string getBillingCity()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingCity(string $value)
 * @method string getBillingPostcode()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingPostcode(string $value)
 * @method string getBillingState()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingState(string $value)
 * @method string getBillingCountry()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingCountry(string $value)
 * @method int getBillingAddressFormatId()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setBillingAddressFormatId(int $value)
 * @method string getPaymentMethod()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setPaymentMethod(string $value)
 * @method string getCcType()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCcType(string $value)
 * @method string getCcOwner()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCcOwner(string $value)
 * @method string getCcNumber()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCcNumber(string $value)
 * @method string getCcExpires()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCcExpires(string $value)
 * @method string getLastModified()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setLastModified(string $value)
 * @method string getDatePurchased()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setDatePurchased(string $value)
 * @method string getOrdersStatus()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setOrdersStatus(string $value)
 * @method string getOrdersDateFinished()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setOrdersDateFinished(string $value)
 * @method string getCurrency()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCurrency(string $value)
 * @method float getCurrencyValue()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCurrencyValue(float $value)
 * @method string getCurrencySymbol()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setCurrencySymbol(string $value)
 * @method float getOrdersTotal()
 * @method Mage_Oscommerce_Model_Oscommerce_Order setOrdersTotal(float $value)
 *
 * @category    Mage
 * @package     Mage_Oscommerce
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Model_Oscommerce_Order extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce_order');
    }

    public function getProducts()
    {
        return $this->getResource()->getProducts();
    }
    
    public function getTotal()
    {
        return $this->getResource()->getTotal();
    }

    public function getComments()
    {
        return $this->getResource()->getComments();
    }

}

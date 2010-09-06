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
 * @method Mage_Oscommerce_Model_Oscommerce_Order getOrdersId()
 * @method int setOrdersId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersId()
 * @method int setCustomersId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getMagentoCustomersId()
 * @method int setMagentoCustomersId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getImportId()
 * @method int setImportId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getWebsiteId()
 * @method int setWebsiteId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersName()
 * @method string setCustomersName(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersCompany()
 * @method string setCustomersCompany(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersStreetAddress()
 * @method string setCustomersStreetAddress(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersSuburb()
 * @method string setCustomersSuburb(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersCity()
 * @method string setCustomersCity(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersPostcode()
 * @method string setCustomersPostcode(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersState()
 * @method string setCustomersState(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersCountry()
 * @method string setCustomersCountry(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersTelephone()
 * @method string setCustomersTelephone(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersEmailAddress()
 * @method string setCustomersEmailAddress(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCustomersAddressFormatId()
 * @method int setCustomersAddressFormatId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryName()
 * @method string setDeliveryName(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryCompany()
 * @method string setDeliveryCompany(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryStreetAddress()
 * @method string setDeliveryStreetAddress(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliverySuburb()
 * @method string setDeliverySuburb(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryCity()
 * @method string setDeliveryCity(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryPostcode()
 * @method string setDeliveryPostcode(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryState()
 * @method string setDeliveryState(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryCountry()
 * @method string setDeliveryCountry(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDeliveryAddressFormatId()
 * @method int setDeliveryAddressFormatId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingName()
 * @method string setBillingName(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingCompany()
 * @method string setBillingCompany(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingStreetAddress()
 * @method string setBillingStreetAddress(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingSuburb()
 * @method string setBillingSuburb(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingCity()
 * @method string setBillingCity(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingPostcode()
 * @method string setBillingPostcode(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingState()
 * @method string setBillingState(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingCountry()
 * @method string setBillingCountry(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getBillingAddressFormatId()
 * @method int setBillingAddressFormatId(int $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getPaymentMethod()
 * @method string setPaymentMethod(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCcType()
 * @method string setCcType(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCcOwner()
 * @method string setCcOwner(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCcNumber()
 * @method string setCcNumber(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCcExpires()
 * @method string setCcExpires(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getLastModified()
 * @method string setLastModified(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getDatePurchased()
 * @method string setDatePurchased(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getOrdersStatus()
 * @method string setOrdersStatus(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getOrdersDateFinished()
 * @method string setOrdersDateFinished(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCurrency()
 * @method string setCurrency(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCurrencyValue()
 * @method float setCurrencyValue(float $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getCurrencySymbol()
 * @method string setCurrencySymbol(string $value)
 * @method Mage_Oscommerce_Model_Oscommerce_Order getOrdersTotal()
 * @method float setOrdersTotal(float $value)
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

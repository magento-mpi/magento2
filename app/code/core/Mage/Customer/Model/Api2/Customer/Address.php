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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer address
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Api2_Customer_Address extends Mage_Api2_Model_Resource
{
    /**
     * Separator for multistreet
     */
    const STREET_SEPARATOR = '; ';

    /**
     * Find region identifier by its name
     *
     * @param string $countryId Country identifier
     * @param string $region Region name
     * @return string
     */
    protected function _getRegionId($countryId, $region)
    {
        // convert region name into ID
        $countries = $this->_getValidator()->getCountryRegions($countryId);

        return isset($countries[$region]) ? $countries[$region] : $region;
    }

    /**
     * Resource specific method to retrieve attributes' codes. May be overriden in child.
     *
     * @return array
     */
    protected function _getResourceAttributes()
    {
        return $this->getEavAttributes(Mage_Api2_Model_Auth_User_Admin::USER_TYPE != $this->getUserType());
    }

    /**
     * Get customer address resource validator instance
     *
     * @return Mage_Customer_Model_Api2_Customer_Address_Validator
     */
    protected function _getValidator()
    {
        return Mage::getSingleton(
            'customer/api2_customer_address_validator', array('resource' => $this, 'operation' => self::OPERATION_CREATE
        ));
    }

    /**
     * Is specified address a default billing address?
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultBillingAddress(Mage_Customer_Model_Address $address)
    {
        return $address->getCustomer()->getDefaultBilling() == $address->getId();
    }

    /**
     * Is specified address a default shipping address?
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultShippingAddress(Mage_Customer_Model_Address $address)
    {
        return $address->getCustomer()->getDefaultShipping() == $address->getId();
    }
}

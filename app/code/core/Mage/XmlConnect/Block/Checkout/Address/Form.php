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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address form xml renderer for onepage checkout
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Address_Form extends Mage_Core_Block_Template
{
    /**
     * Render customer address form xml
     *
     * @return string
     */
    protected function _toHtml()
    {

        $helper   = Mage::helper('xmlconnect');
        $address  = $this->getAddress();
        $xmlModel = new Mage_XmlConnect_Model_Simplexml_Element('<node></node>');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $addressType = $this->getType() == 'shipping' || $this->getType() == 'billing' ? $this->getType() : 'billing';
        $isAllowedGuestCheckout= Mage::getSingleton('checkout/session')->getQuote()->isAllowedGuestCheckout();
        if($addressType == 'shipping'){
            $addressId = $customer->getDefaultShipping();
            $address   = $customer->getAddressById($addressId);
        }
        else{
            $addressId = $customer->getDefaultBilling();
            $address   = $customer->getAddressById($addressId);
        }

        if ($addressId && $address && $address->getId()) {

            $firstname  = $xmlModel->xmlentities(strip_tags($address->getFirstname()));
            $lastname   = $xmlModel->xmlentities(strip_tags($address->getLastname()));
            $company    = $xmlModel->xmlentities(strip_tags($address->getCompany()));
            if ($isAllowedGuestCheckout) {
                $email  = $xmlModel->xmlentities(strip_tags($address->getEmail()));
            }
            $street1    = $xmlModel->xmlentities(strip_tags($address->getStreet(1)));
            $street2    = $xmlModel->xmlentities(strip_tags($address->getStreet(2)));
            $city       = $xmlModel->xmlentities(strip_tags($address->getCity()));
            $regionId   = $xmlModel->xmlentities($address->getRegionId());
            $region = Mage::getModel('directory/region')->load($regionId)->getName();
            if (!$region) {
                $region = $address->getRegion();
            }
            $region     = $xmlModel->xmlentities(strip_tags($region));
            $postcode   = $xmlModel->xmlentities(strip_tags($address->getPostcode()));
            $countryId  = $xmlModel->xmlentities($address->getCountryId());
            $telephone  = $xmlModel->xmlentities(strip_tags($address->getTelephone()));
            $fax        = $xmlModel->xmlentities(strip_tags($address->getFax()));
        }
        else {
            $firstname = $lastname = $company = $email = $street1 = $street2 = '';
            $city = $region = $postcode = $telephone = $fax = '';
            $countryId = $regionId = null;
        }

        $countries = $this->_getCountryOptions();

        $regions = array();
        $countryOptionsXml = '<values>';
        if (is_array($countries)) {
            foreach ($countries as $key => $data) {
                if ($data['value']) {
                    $regions = $this->_getRegionOptions($data['value']);
                }
                $countryOptionsXml .= '
                <item relation="' . (is_array($regions) && !empty($regions) ? 'region_id' : 'region') . '"' . ($countryId == $data['value'] ? ' selected="1"' : '') . '>
                    <label>' . $xmlModel->xmlentities((string)$data['label']) . '</label>
                    <value>' . $xmlModel->xmlentities($data['value']) . '</value>';
                if (is_array($regions) && !empty($regions)) {
                    $countryOptionsXml .= '<regions>';
                    foreach ($regions as $_key => $_data){
                        $countryOptionsXml .= '<region_item' . ($regionId == $_data['value'] ? ' selected="1"' : '') . '>';
                        $countryOptionsXml .=
                            '<label>' . $xmlModel->xmlentities((string)$_data['label']) . '</label>
                             <value>' . $xmlModel->xmlentities($_data['value']) . '</value>';
                        $countryOptionsXml .= '</region_item>';
                    }
                    $countryOptionsXml .= '</regions>';
                }
                $countryOptionsXml .= '</item>';
            }
        }
        $countryOptionsXml .= '</values>';

        $xml = <<<EOT
<form name="address_form" method="post">
        <field name="{$addressType}[firstname]" type="text" label="{$helper->__('First Name')}" required="true" value="$firstname" />
        <field name="{$addressType}[lastname]" type="text" label="{$helper->__('Last Name')}" required="true" value="$lastname" />
        <field name="{$addressType}[company]" type="text" label="{$helper->__('Company')}" value="$company" />
EOT;
        if ($isAllowedGuestCheckout) {
            $xml .= <<<EOT
        <field name="{$addressType}[email]" type="text" label="{$helper->__('Email Address')}" value="$email" />
EOT;
        }
        $xml .= <<<EOT
        <field name="{$addressType}[street][]" type="text" label="{$helper->__('Address')}" required="true" value="$street1" />
        <field name="{$addressType}[street][]" type="text" label="{$helper->__('Address 2')}" value="$street2" />
        <field name="{$addressType}[city]" type="text" label="{$helper->__('City')}" required="true" value="$city" />
        <field name="{$addressType}[country_id]" type="select" label="{$helper->__('Country')}" required="true">
            $countryOptionsXml
        </field>
        <field name="{$addressType}[region]" type="text" label="{$helper->__('State/Province')}" value="$region" />
        <field name="{$addressType}[region_id]" type="select" label="{$helper->__('State/Province')}" required="true" />
        <field name="{$addressType}[postcode]" type="text" label="{$helper->__('Zip/Postal Code')}" required="true" value="$postcode" />
        <field name="{$addressType}[telephone]" type="text" label="{$helper->__('Telephone')}" required="true" value="$telephone" />
        <field name="{$addressType}[fax]" type="text" label="{$helper->__('Fax')}" value="$fax" />
        <field name="{$addressType}[save_in_address_book]" type="checkbox" label="{$helper->__('Save in address book')}"/>
</form>
EOT;
        return $xml;
    }

    /**
     * Retrieve regions by country
     *
     * @param string $countryId
     * @return array
     */
    protected function _getRegionOptions($countryId)
    {
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE'.Mage::app()->getStore()->getId().$countryId;
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        }
        else {
            $collection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($countryId)
                ->load();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }

    /**
     * Retrieve countries
     *
     * @return array
     */
    protected function _getCountryOptions()
    {
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_'.Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        }
        else {
            $collection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }
}

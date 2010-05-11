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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address book xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Address_List extends Mage_Core_Block_Template
{

    /**
     * Address attribute list to retrieve
     *
     * @var array
     */
    protected $_addressAttributes = array(
        'entity_id',
        'firstname',
        'lastname',
        'company',
        'street1',
        'street2',
        'city',
        'region',
        'region_id',
        'postcode',
        'country',
        'country_id',
        'telephone',
        'fax'
    );

    /**
     * Render customer address list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $addressXmlObj      = new Varien_Simplexml_Element('<address></address>');
        $_billingAddsses    = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
        $_shippingAddsses   = Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();

        if($_billingAddsses){
            $item = $addressXmlObj->addChild('item');
            $item->addAttribute('label', $this->__('Default Billing Address'));
            $item->addAttribute('default_billing', 1);
            $this->prepareAddressData(Mage::getSingleton('customer/session')->getCustomer()->getAddressById($_billingAddsses), $item);
        }
        if ($_shippingAddsses) {
            $item = $addressXmlObj->addChild('item');
            $item->addAttribute('label', $this->__('Default Shipping Address'));
            $item->addAttribute('default_shipping', 1);
            $this->prepareAddressData(Mage::getSingleton('customer/session')->getCustomer()->getAddressById($_shippingAddsses), $item);
        }
        $_additionalAddresses = Mage::getSingleton('customer/session')->getCustomer()->getAdditionalAddresses();
        if($_additionalAddresses){
            foreach($_additionalAddresses as $_address){
                $item = $addressXmlObj->addChild('item');
                $item->addAttribute('label', $this->__('Additional Address'));
                $item->addAttribute('additional', 1);
                $this->prepareAddressData($_address, $item);
            }
        }

        return $addressXmlObj->asNiceXml();
    }

    /**
     * Collect address data to xml node
     * Remove objects from data array and escape data values
     *
     * @param Mage_Customer_Model_Address $address
     * @return array
     * @see $this->_addressAttributes
     */
    public function prepareAddressData(Mage_Customer_Model_Address $address, Varien_Simplexml_Element $item)
    {
        if (!$address) {
        	return array();
        }

        $address->explodeStreetAddress();
        $data = $address->getData();
        $data['country'] = $address->getCountryModel()->getName();

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                unset($data[$key]);
            }
            else {
                $data[$key] = $item->xmlentities(strip_tags($value));
            }
        }

        /**
         * Remove data that mustn't show
         */
//        if (!$this->helper('customer/address')->canShowConfig('prefix_show')) {
//            unset($data['prefix']);
//        }
//        if (!$this->helper('customer/address')->canShowConfig('middlename_show')) {
//            unset($data['middlename']);
//        }
//        if (!$this->helper('customer/address')->canShowConfig('suffix_show')) {
//            unset($data['suffix']);
//        }

        $data = array_intersect_key($data, array_flip($this->_addressAttributes));
        foreach ($data as $key => $value) {
            if (!empty($value)) {
            	$item->addChild($key, $value);
            }
        }
    }
}
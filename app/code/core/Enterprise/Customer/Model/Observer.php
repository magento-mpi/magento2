<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer observer
 *
 */
class Enterprise_Customer_Model_Observer
{
    /**
     * After load observer for quote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterLoadSalesQuote($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Mage_Core_Model_Abstract){
            Mage::getModel('enterprise_customer/sales_quote')
                ->load($quote->getId())
                ->attachAttributeData($quote);
        }

        return $this;
    }

    /**
     * After load observer for collection of quote address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterLoadSalesQuoteAddressCollection($observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof Varien_Data_Collection_Db){
            Mage::getModel('enterprise_customer/sales_quote_address')
                ->attachDataToCollection($collection);
        }

        return $this;
    }

    /**
     * After save observer for quote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterSaveSalesQuote($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Mage_Core_Model_Abstract){
            Mage::getModel('enterprise_customer/sales_quote')
                ->saveAttributeData($quote);
        }

        return $this;
    }

    /**
     * After save observer for quote address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterSaveSalesQuoteAddress($observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof Mage_Core_Model_Abstract){
            Mage::getModel('enterprise_customer/sales_quote_address')
                ->saveAttributeData($quoteAddress);
        }
        
        return $this;
    }

    /**
     * After load observer for order
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterLoadSalesOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Mage_Core_Model_Abstract){
            Mage::getModel('enterprise_customer/sales_order')
                ->load($order->getId())
                ->attachAttributeData($order);
        }

        return $this;
    }

    /**
     * After load observer for collection of order address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterLoadSalesOrderAddressCollection($observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof Varien_Data_Collection_Db){
            Mage::getModel('enterprise_customer/sales_order_address')
                ->attachDataToCollection($collection);
        }

        return $this;
    }

    /**
     * After save observer for order
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterSaveSalesOrder($observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Mage_Core_Model_Abstract){
            Mage::getModel('enterprise_customer/sales_order')
                ->saveAttributeData($order);
        }

        return $this;
    }

    /**
     * After save observer for order address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function afterSaveSalesOrderAddress($observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof Mage_Core_Model_Abstract){
            Mage::getModel('enterprise_customer/sales_order_address')
                ->saveAttributeData($orderAddress);
        }

        return $this;
    }

    /**
     * After save observer for customer attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function EnterpriseCustomerAttributeSave($observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Mage_Customer_Model_Attribute
            && $attribute->isObjectNew()
        ) {
            Mage::getModel('enterprise_customer/sales_quote')
                ->saveNewAttribute($attribute);
            Mage::getModel('enterprise_customer/sales_order')
                ->saveNewAttribute($attribute);
        }

        return $this;
    }

    /**
     * After delete observer for customer attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function EnterpriseCustomerAttributeDelete($observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Mage_Customer_Model_Attribute 
            && !$attribute->isObjectNew()
        ) {
            Mage::getModel('enterprise_customer/sales_quote')
                ->deleteAttribute($attribute);
            Mage::getModel('enterprise_customer/sales_order')
                ->deleteAttribute($attribute);
        }

        return $this;
    }

    /**
     * After save observer for customer address attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function EnterpriseCustomerAddressAttributeSave($observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Mage_Customer_Model_Attribute
            && $attribute->isObjectNew()
        ) {
            Mage::getModel('enterprise_customer/sales_quote_address')
                ->saveNewAttribute($attribute);
            Mage::getModel('enterprise_customer/sales_order_address')
                ->saveNewAttribute($attribute);
        }

        return $this;
    }

    /**
     * After delete observer for customer address attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function EnterpriseCustomerAddressAttributeDelete($observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Mage_Customer_Model_Attribute
            && !$attribute->isObjectNew()
        ) {
            Mage::getModel('enterprise_customer/sales_quote_address')
                ->deleteAttribute($attribute);
            Mage::getModel('enterprise_customer/sales_order_address')
                ->deleteAttribute($attribute);
        }
        
        return $this;
    }

    /**
     * Observer for converting quote to order
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function salesConvertQuoteToOrder($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        if ($quote instanceof Mage_Core_Model_Abstract
            && $order instanceof Mage_Core_Model_Abstract
        ){
            Mage::getModel('enterprise_customer/sales_quote')
                    ->convertQuoteToOrder($quote, $order);
        }
        
        return $this;
    }

    /**
     * Observer for converting quote address to order address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function salesConvertQuoteAddressToOrderAddress($observer)
    {
        $quoteAddress = $observer->getEvent()->getAddress();
        $orderAddress = $observer->getEvent()->getOrderAddress();
        if ($quoteAddress instanceof Mage_Core_Model_Abstract
            && $orderAddress instanceof Mage_Core_Model_Abstract
        ){
            Mage::getModel('enterprise_customer/sales_quote_address')
                    ->convertQuoteToOrder($quoteAddress, $orderAddress);
        }
        
        return $this;
    }

    /**
     * Observer for converting order to quote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function salesConvertOrderToQuote($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        if ($quote instanceof Mage_Core_Model_Abstract 
            && $order instanceof Mage_Core_Model_Abstract
        ){
            Mage::getModel('enterprise_customer/sales_order')
                    ->convertOrderToQuote($order, $quote);
        }

        return $this;
    }

    /**
     * Observer for converting order address to quote address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function salesConvertOrderAddressToQuoteAddress($observer)
    {
        $quoteAddress = $observer->getEvent()->getAddress();
        $orderAddress = $observer->getEvent()->getOrderAddress();
        if ($quoteAddress instanceof Mage_Core_Model_Abstract
            && $orderAddress instanceof Mage_Core_Model_Abstract
        ){
            
            Mage::getModel('enterprise_customer/sales_order_address')
                    ->convertOrderToQuote($orderAddress, $quoteAddress);
        }

        return $this;
    }
}

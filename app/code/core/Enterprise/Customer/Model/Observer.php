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
    public function afterLoadSalesQuote(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Mage_Core_Model_Abstract) {
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
    public function afterLoadSalesQuoteAddressCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getQuoteAddressCollection();
        if ($collection instanceof Varien_Data_Collection_Db) {
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
    public function afterSaveSalesQuote(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof Mage_Core_Model_Abstract) {
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
    public function afterSaveSalesQuoteAddress(Varien_Event_Observer $observer)
    {
        $quoteAddress = $observer->getEvent()->getQuoteAddress();
        if ($quoteAddress instanceof Mage_Core_Model_Abstract) {
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
    public function afterLoadSalesOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Mage_Core_Model_Abstract) {
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
    public function afterLoadSalesOrderAddressCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderAddressCollection();
        if ($collection instanceof Varien_Data_Collection_Db) {
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
    public function afterSaveSalesOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Mage_Core_Model_Abstract) {
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
    public function afterSaveSalesOrderAddress(Varien_Event_Observer $observer)
    {
        $orderAddress = $observer->getEvent()->getAddress();
        if ($orderAddress instanceof Mage_Core_Model_Abstract) {
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
    public function EnterpriseCustomerAttributeSave(Varien_Event_Observer $observer)
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
    public function EnterpriseCustomerAttributeDelete(Varien_Event_Observer $observer)
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
    public function EnterpriseCustomerAddressAttributeSave(Varien_Event_Observer $observer)
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
    public function EnterpriseCustomerAddressAttributeDelete(Varien_Event_Observer $observer)
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
    public function coreCopyFieldsetSalesConvertQuoteToOrder(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_quote')
        );

        return $this;
    }

    /**
     * Observer for converting quote address to order address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_quote_address')
        );

        return $this;
    }

    /**
     * Observer for converting order to quote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function coreCopyFieldsetSalesCopyOrderToEdit(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_order')
        );

        return $this;
    }

    /**
     * Observer for converting order billing address to quote billing address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function coreCopyFieldsetSalesCopyOrderBillingAddressToOrder(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_order_address')
        );

        return $this;
    }

    /**
     * Observer for converting order shipping address to quote shipping address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function coreCopyFieldsetSalesCopyOrderShippingAddressToOrder(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_order_address')
        );

        return $this;
    }

    /**
     * Observer for converting customer to quote
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function coreCopyFieldsetCustomerAccountToQuote(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_quote'),
            Mage::getModel('enterprise_customer/sales_quote')->describeTable(),
            true
        );
        
        return $this;
    }

    /**
     * Observer for converting customer address to quote address
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Customer_Model_Observer
     */
    public function coreCopyFieldsetCustomerAddressToQuoteAddress(Varien_Event_Observer $observer)
    {
        $this->_coreCopyFieldsetSourceToTarget(
            $observer,
            Mage::getModel('enterprise_customer/sales_quote_address'),
            Mage::getModel('enterprise_customer/sales_quote_address')->describeTable()
        );

        return $this;
    }

    /**
     * CopyFieldset converts customer attributes from source object to target object
     *
     * @param Varien_Event_Observer $observer
     * @param Mage_Core_Model_Abstract $model
     * @param array $fields
     * @param bool $useColumnPrefix
     * @return Enterprise_Customer_Model_Observer
     */
    protected function _coreCopyFieldsetSourceToTarget(
        Varien_Event_Observer    $observer,
        Mage_Core_Model_Abstract $model,
        array                    $fields = null,
                                 $useColumnPrefix = false
    ) {
        $source = $observer->getEvent()->getSource();
        $target = $observer->getEvent()->getTarget();
        if ($source instanceof Mage_Core_Model_Abstract
            && $target instanceof Mage_Core_Model_Abstract
        ) {
            $model->copyFieldsetSourceToTarget($source, $target, $fields, $useColumnPrefix);
        }

        return $this;
    }
}

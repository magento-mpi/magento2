<?php
/**
 * Google AdWords module observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GoogleAdwords_Model_Observer
{
    /**
     * @var Magento_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Sales_Model_Resource_Order_Collection
     */
    protected $_collection;

    /**
     * Constructor
     *
     * @param Magento_GoogleAdwords_Helper_Data $helper
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Sales_Model_Resource_Order_Collection $collection
     */
    public function __construct(
        Magento_GoogleAdwords_Helper_Data $helper,
        Magento_Core_Model_Registry $registry,
        Magento_Sales_Model_Resource_Order_Collection $collection
    ) {
        $this->_helper = $helper;
        $this->_collection = $collection;
        $this->_registry = $registry;
    }

    /**
     * Set base grand total of order to registry
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_GoogleAdwords_Model_Observer
     */
    public function setConversionValue(Magento_Event_Observer $observer)
    {
        if (!($this->_helper->isGoogleAdwordsActive() && $this->_helper->isDynamicConversionValue())) {
            return $this;
        }
        $orderIds = $observer->getEvent()->getOrderIds();
        if (!$orderIds || !is_array($orderIds)) {
            return $this;
        }
        $this->_collection->addFieldToFilter('entity_id', array('in' => $orderIds));
        $conversionValue = 0;
        /** @var $order Magento_Sales_Model_Order */
        foreach ($this->_collection as $order) {
            $conversionValue += $order->getBaseGrandTotal();
        }
        $this->_registry->register(Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_REGISTRY_NAME, $conversionValue);
        return $this;
    }
}

<?php
/**
 * Google AdWords module observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_GoogleAdwords_Model_Observer
{
    /**
     * @var Mage_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Sales_Model_Resource_Order_Collection
     */
    protected $_collection;

    /**
     * Constructor
     *
     * @param Mage_GoogleAdwords_Helper_Data $helper
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     */
    public function __construct(
        Mage_GoogleAdwords_Helper_Data $helper,
        Mage_Core_Model_Registry $registry,
        Mage_Sales_Model_Resource_Order_Collection $collection
    ) {
        $this->_helper = $helper;
        $this->_collection = $collection;
        $this->_registry = $registry;
    }

    /**
     * Set base grand total of order to registry
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_GoogleAdwords_Model_Observer
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
        /** @var $order Mage_Sales_Model_Order */
        foreach ($this->_collection as $order) {
            $conversionValue += $order->getBaseGrandTotal();
        }
        $this->_registry->register(Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_REGISTRY_NAME, $conversionValue);
        return $this;
    }
}

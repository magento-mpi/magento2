<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat sales order payment collection
 */
class Magento_Sales_Model_Resource_Order_Payment_Collection extends Magento_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_payment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_payment_collection';

    /**
     * @var Magento_Sales_Model_Payment_Method_Converter
     */
    protected $_converter;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Sales_Model_Payment_Method_Converter $converter
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Sales_Model_Payment_Method_Converter $converter,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $fetchStrategy, $resource);
        $this->_converter = $converter;
    }

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order_Payment', 'Magento_Sales_Model_Resource_Order_Payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return Magento_Sales_Model_Resource_Order_Payment_Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        /** @var Magento_Sales_Model_Order_Payment $item */
        foreach ($this->_items as $item) {
            foreach ($item->getData() as $fieldName => $fieldValue) {
                $item->setData($fieldName,
                    $this->_converter->decode($item, $fieldName)
                );
            }
        }

        return parent::_afterLoad();
    }
}

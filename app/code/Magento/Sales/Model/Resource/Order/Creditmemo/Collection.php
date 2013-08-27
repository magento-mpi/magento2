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
 * Flat sales order creditmemo collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Creditmemo_Collection extends Magento_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order_Creditmemo', 'Magento_Sales_Model_Resource_Order_Creditmemo');
    }

    /**
     * Used to emulate after load functionality for each item without loading them
     *
     * @return Magento_Sales_Model_Resource_Order_Creditmemo_Collection
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        return $this;
    }

    /**
     * Add filtration conditions
     *
     * @param array|null $filter
     * @return Magento_Sales_Model_Resource_Order_Creditmemo_Collection
     */
    public function getFiltered($filter = null)
    {
        if (is_array($filter)) {
            foreach ($filter as $field => $value) {
                $this->addFieldToFilter($field, $value);
            }
        }
        return $this;
    }
}

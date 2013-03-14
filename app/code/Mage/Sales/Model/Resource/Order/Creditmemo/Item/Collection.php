<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order creditmemo items collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_item_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Order_Creditmemo_Item', 'Mage_Sales_Model_Resource_Order_Creditmemo_Item');
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return Mage_Sales_Model_Resource_Order_Creditmemo_Item_Collection
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        $this->addFieldToFilter('parent_id', $creditmemoId);
        return $this;
    }
}

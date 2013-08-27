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
 * Flat sales order creditmemo comments collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Creditmemo_Comment_Collection
    extends Magento_Sales_Model_Resource_Order_Comment_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_creditmemo_comment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_creditmemo_comment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_Sales_Model_Order_Creditmemo_Comment', 'Magento_Sales_Model_Resource_Order_Creditmemo_Comment');
    }

    /**
     * Set creditmemo filter
     *
     * @param int $creditmemoId
     * @return Magento_Sales_Model_Resource_Order_Creditmemo_Comment_Collection
     */
    public function setCreditmemoFilter($creditmemoId)
    {
        return $this->setParentFilter($creditmemoId);
    }
}

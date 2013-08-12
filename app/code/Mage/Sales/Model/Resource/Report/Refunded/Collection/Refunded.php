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
 * Sales report refunded collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report_Refunded_Collection_Refunded
    extends Mage_Sales_Model_Resource_Report_Refunded_Collection_Order
{
    /**
     * Initialize custom resource model
     *
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Sales_Model_Resource_Report $resource
    ) {
        $resource->init('sales_refunded_aggregated');
        parent::__construct($fetchStrategy, $resource);
    }
}

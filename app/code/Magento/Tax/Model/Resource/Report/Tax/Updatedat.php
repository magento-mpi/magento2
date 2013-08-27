<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax report resource model with aggregation by updated at
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Resource_Report_Tax_Updatedat extends Magento_Tax_Model_Resource_Report_Tax_Createdat
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_order_aggregated_updated', 'id');
    }

    /**
     * Aggregate Tax data by order updated at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Tax_Model_Resource_Report_Tax_Updatedat
     */
    public function aggregate($from = null, $to = null)
    {
        return $this->_aggregateByOrder('updated_at', $from, $to);
    }
}

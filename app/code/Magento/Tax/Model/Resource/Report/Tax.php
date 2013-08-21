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
 * Tax report resource model
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Resource_Report_Tax extends Magento_Reports_Model_Resource_Report_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Tax_Model_Resource_Report_Tax
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('Magento_Tax_Model_Resource_Report_Tax_Createdat')->aggregate($from, $to);
        Mage::getResourceModel('Magento_Tax_Model_Resource_Report_Tax_Updatedat')->aggregate($from, $to);
        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_TAX_FLAG_CODE);

        return $this;
    }
}

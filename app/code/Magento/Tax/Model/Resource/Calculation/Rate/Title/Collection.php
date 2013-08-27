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
 * Tax Rate Title Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Resource_Calculation_Rate_Title_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Calculation_Rate_Title', 'Magento_Tax_Model_Resource_Calculation_Rate_Title');
    }

    /**
     * Add rate id filter
     *
     * @param int $rateId
     * @return Magento_Tax_Model_Resource_Calculation_Rate_Title_Collection
     */
    public function loadByRateId($rateId)
    {
        $this->addFieldToFilter('main_table.tax_calculation_rate_id', $rateId);
        return $this->load();
    }
}

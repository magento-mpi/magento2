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
 * Tax rate resource model
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Resource_Calculation_Rate extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_calculation_rate', 'tax_calculation_rate_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_Tax_Model_Resource_Calculation_Rate
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('code'),
            'title' => __('Code'),
        ));
        return $this;
    }

    /**
     * Delete all rates
     *
     * @return Magento_Tax_Model_Resource_Calculation_Rate
     */
    public function deleteAllRates()
    {
        $this->_getWriteAdapter()->delete($this->getMainTable());
        return $this;
    }

    /**
     * Check if this rate exists in rule
     *
     * @param  int $rateId
     * @return array
     */
    public function isInRule($rateId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('tax_calculation'), array('tax_calculation_rate_id'))
            ->where('tax_calculation_rate_id = ?', $rateId);
        return $adapter->fetchCol($select);
    }
}

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
 * Tax Rate Title Model
 *
 * @method Magento_Tax_Model_Resource_Calculation_Rate_Title _getResource()
 * @method Magento_Tax_Model_Resource_Calculation_Rate_Title getResource()
 * @method int getTaxCalculationRateId()
 * @method Magento_Tax_Model_Calculation_Rate_Title setTaxCalculationRateId(int $value)
 * @method int getStoreId()
 * @method Magento_Tax_Model_Calculation_Rate_Title setStoreId(int $value)
 * @method string getValue()
 * @method Magento_Tax_Model_Calculation_Rate_Title setValue(string $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Calculation_Rate_Title extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Resource_Calculation_Rate_Title');
    }

    public function deleteByRateId($rateId)
    {
        $this->getResource()->deleteByRateId($rateId);
        return $this;
    }
}

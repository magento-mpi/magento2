<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Title Model
 *
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title _getResource()
 * @method Mage_Tax_Model_Resource_Calculation_Rate_Title getResource()
 * @method int getTaxCalculationRateId()
 * @method Mage_Tax_Model_Calculation_Rate_Title setTaxCalculationRateId(int $value)
 * @method int getStoreId()
 * @method Mage_Tax_Model_Calculation_Rate_Title setStoreId(int $value)
 * @method string getValue()
 * @method Mage_Tax_Model_Calculation_Rate_Title setValue(string $value)
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Calculation_Rate_Title extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Tax_Model_Resource_Calculation_Rate_Title');
    }

    public function deleteByRateId($rateId)
    {
        $this->getResource()->deleteByRateId($rateId);
        return $this;
    }
}

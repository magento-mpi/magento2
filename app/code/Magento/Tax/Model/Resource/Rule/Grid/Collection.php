<?php
/**
 * Tax Rule collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Resource_Rule_Grid_Collection extends Magento_Tax_Model_Resource_Calculation_Rule_Collection
{
    /**
     * @return Magento_Tax_Model_Resource_Rule_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCustomerTaxClassesToResult();
        $this->addProductTaxClassesToResult();
        $this->addRatesToResult();

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return Magento_Tax_Model_Resource_Rule_Grid_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case 'rate.tax_calculation_rate_id':
                $this->joinCalculationData('rate');
                break;

            case 'ctc.customer_tax_class_id':
                $this->joinCalculationData('ctc');
                break;

            case 'ptc.product_tax_class_id':
                $this->joinCalculationData('ptc');
                break;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}

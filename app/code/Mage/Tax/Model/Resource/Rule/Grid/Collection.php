<?php
    /**
     * {license_notice}
     *
     * @category    Mage
     * @package     Mage_Tax
     * @copyright   {copyright}
     * @license     {license_link}
     */
class Mage_Tax_Model_Resource_Rule_Grid_Collection extends Mage_Tax_Model_Resource_Calculation_Rule_Collection
{
    /**
     * @return Mage_Tax_Model_Resource_Rule_Grid_Collection
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
     * @param array|null|string $condition
     * @return Varien_Data_Collection_Db
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

<?php
/**
 * Tax Rule collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Rule\Grid;

class Collection extends \Magento\Tax\Model\Resource\Calculation\Rule\Collection
{
    /**
     * @return \Magento\Tax\Model\Resource\Rule\Grid\Collection
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
     * @return \Magento\Tax\Model\Resource\Rule\Grid\Collection
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

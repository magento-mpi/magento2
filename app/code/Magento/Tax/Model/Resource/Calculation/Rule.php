<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Calculation;

/**
 * Tax rate resource model
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rule extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tax_calculation_rule', 'tax_calculation_rule_id');
    }

    /**
     * Initialize unique fields
     *
     * @return \Magento\Tax\Model\Resource\Calculation\Rule
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array('field' => array('code'), 'title' => __('Code')));
        return $this;
    }

    /**
     * Fetches rules by rate, customer tax classes and product tax classes.  Returns array of rule codes.
     *
     * @param array $rateId
     * @param array $customerTaxClassIds
     * @param array $productTaxClassIds
     * @return array
     */
    public function fetchRuleCodes($rateId, $customerTaxClassIds, $productTaxClassIds)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('main' => $this->getTable('tax_calculation')), null)
            ->joinLeft(
                array('d' => $this->getTable('tax_calculation_rule')),
                'd.tax_calculation_rule_id = main.tax_calculation_rule_id',
                array('d.code')
            )
            ->where('main.tax_calculation_rate_id in (?)', $rateId)
            ->where('main.customer_tax_class_id in (?)', $customerTaxClassIds)
            ->where('main.product_tax_class_id in (?)', $productTaxClassIds)
            ->distinct(true);

        return $adapter->fetchCol($select);
    }
}

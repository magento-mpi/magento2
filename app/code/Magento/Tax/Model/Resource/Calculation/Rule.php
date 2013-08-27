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
class Magento_Tax_Model_Resource_Calculation_Rule extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_calculation_rule', 'tax_calculation_rule_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_Tax_Model_Resource_Calculation_Rule
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => array('code'),
            'title' => __('Code'),
        ));
        return $this;
    }
}

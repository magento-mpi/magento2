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
 * Tax Calculation Collection
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Resource_Calculation_Grid_Collection extends Magento_Tax_Model_Resource_Calculation_Rate_Collection
{
    /**
     * Join Region Table
     *
     * @return string
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinRegionTable();
        return $this;
    }
}

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
 * Tax Calculation Collection
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Calculation_Grid_Collection extends Mage_Tax_Model_Resource_Calculation_Rate_Collection
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

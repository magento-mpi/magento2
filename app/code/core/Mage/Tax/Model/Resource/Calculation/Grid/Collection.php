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
     * Resource initialization
     */
    protected function _prepareSelect(Varien_Db_Select $select)
    {
        $this->joinRegionTable();
            return parent::_prepareselect($select);
    }
}

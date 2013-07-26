<?php
/**
 * Customer group collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Model_Resource_Group_Grid_Collection extends Mage_Customer_Model_Resource_Group_Collection
{
    /**
     * Resource initialization
     * @return Mage_Customer_Model_Resource_Group_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addTaxClass();
        return $this;
    }
}

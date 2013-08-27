<?php
/**
 * Customer group collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Customer_Model_Resource_Group_Grid_Collection extends Magento_Customer_Model_Resource_Group_Collection
{
    /**
     * Resource initialization
     * @return Magento_Customer_Model_Resource_Group_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addTaxClass();
        return $this;
    }
}

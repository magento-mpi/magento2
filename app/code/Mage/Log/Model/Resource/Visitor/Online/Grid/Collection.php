<?php
/**
 * Log Online visitors collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Log_Model_Resource_Visitor_Online_Grid_Collection extends Mage_Log_Model_Resource_Visitor_Online_Collection
{
    /**
     *
     * @var array
     * @return Mage_Log_Model_Resource_Visitor_Online_Grid_Collection
     */

    protected function _initSelect()
    {
        parent::_initSelect();
        Mage::getModel('Mage_Log_Model_Visitor_Online')
           ->prepare();
        $this->addCustomerData();
        return $this;
    }

}

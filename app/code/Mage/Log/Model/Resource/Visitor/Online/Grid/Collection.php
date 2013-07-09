<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Log Online visitors collection
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->addCustomerData();
        return $this;
    }


}

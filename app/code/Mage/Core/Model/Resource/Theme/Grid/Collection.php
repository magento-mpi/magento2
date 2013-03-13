<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme grid collection
 */
class Mage_Core_Model_Resource_Theme_Grid_Collection extends Mage_Core_Model_Resource_Theme_Collection
{
    /**
     * Add area filter
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Core_Model_Resource_Theme_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)->addParentTitle();
        return $this;
    }
}

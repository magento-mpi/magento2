<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme grid collection
 */
class Magento_Core_Model_Resource_Theme_Grid_Collection extends Magento_Core_Model_Resource_Theme_Collection
{
    /**
     * Add area filter
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Magento_Core_Model_Resource_Theme_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->filterVisibleThemes()->addAreaFilter(Magento_Core_Model_App_Area::AREA_FRONTEND)->addParentTitle();
        return $this;
    }
}

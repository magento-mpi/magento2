<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_CatalogRule_Model_Resource_Grid_Collection
    extends Mage_CatalogRule_Model_Resource_Rule_Collection
{
    /**
     * @return $this|Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        return $this;
    }
}

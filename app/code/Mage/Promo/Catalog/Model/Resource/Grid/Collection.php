<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Promo
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Promo_Catalog_Model_Resource_Grid_Collection
    extends Mage_CatalogRule_Model_Resource_Rule_Collection
{
    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Promo_Catalog_Model_Resource_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        return $this;
    }
}
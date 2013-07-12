<?php
/**
 * {license_notice}
 *
 * Promo Catalog collection resource model
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Promo_Catalog_Model_Resource_Grid_Collection
    extends Mage_CatalogRule_Model_Resource_Rule_Collection
{
    /**
     * @return Mage_Promo_Catalog_Model_Resource_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        return $this;
    }
}
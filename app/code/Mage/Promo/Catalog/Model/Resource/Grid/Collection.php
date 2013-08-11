<?php
/**
 * Promo Catalog collection resource model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Promo_Catalog_Model_Resource_Grid_Collection
    extends Magento_CatalogRule_Model_Resource_Rule_Collection
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
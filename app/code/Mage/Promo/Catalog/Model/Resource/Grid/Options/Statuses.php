<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Promo_Catalog_Model_Resource_Grid_Options_Statuses
    extends Mage_Backend_Model_Config_Source_Activity_Options
{
    /**
     * @var Mage_CatalogRule_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_CatalogRule_Helper_Data $helper
     */
    public function __construct(Mage_CatalogRule_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }
}
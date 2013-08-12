<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Promo_Catalog_Model_Resource_Grid_Options_Statuses
    extends Magento_Backend_Model_Config_Source_Activity_Options
{
    /**
     * @var Magento_CatalogRule_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_CatalogRule_Helper_Data $helper
     */
    public function __construct(Magento_CatalogRule_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }
}
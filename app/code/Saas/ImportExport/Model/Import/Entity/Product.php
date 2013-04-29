<?php
/**
 * Import entity product class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @method Saas_ImportExport_Model_Export_Adapter_Abstract getWriter getWriter()
 */
class Saas_ImportExport_Model_Import_Entity_Product extends Mage_ImportExport_Model_Import_Entity_Product
{
    /**
     * Product entity import constructor
     * @link https://jira.corp.x.com/browse/MAGETWO-9687
     */
    public function __construct()
    {
        $this->_indexValueAttributes = array_merge($this->_indexValueAttributes, array(
            'unit_price_unit',
            'unit_price_base_unit',
        ));
        parent::__construct();
    }
}

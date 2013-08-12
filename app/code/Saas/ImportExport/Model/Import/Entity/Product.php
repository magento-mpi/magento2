<?php
/**
 * Import entity product class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Entity_Product extends Magento_ImportExport_Model_Import_Entity_Product
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

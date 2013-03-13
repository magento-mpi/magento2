<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch attribute layer filter
 *
 */
class Mage_CatalogSearch_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    /**
     * Set filter model name
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Mage_CatalogSearch_Model_Layer_Filter_Attribute';
    }
}

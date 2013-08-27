<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch attribute layer filter
 *
 */
class Magento_CatalogSearch_Block_Layer_Filter_Attribute extends Magento_Catalog_Block_Layer_Filter_Attribute
{
    /**
     * Set filter model name
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento_CatalogSearch_Model_Layer_Filter_Attribute';
    }
}

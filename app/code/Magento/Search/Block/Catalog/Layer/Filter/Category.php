<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog layer category filter
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Block_Catalog_Layer_Filter_Category extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento_Search_Model_Catalog_Layer_Filter_Category';
    }

    /**
     * Add params to faceted search
     *
     * @return Magento_Search_Block_Catalog_Layer_Filter_Category
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

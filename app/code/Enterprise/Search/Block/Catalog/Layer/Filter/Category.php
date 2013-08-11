<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog layer category filter
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalog_Layer_Filter_Category extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Enterprise_Search_Model_Catalog_Layer_Filter_Category';
    }

    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Block_Catalog_Layer_Filter_Category
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

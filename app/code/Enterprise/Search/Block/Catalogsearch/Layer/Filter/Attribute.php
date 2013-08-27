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
 * Catalog attribute layer filter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalogsearch_Layer_Filter_Attribute extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Enterprise_Search_Model_Search_Layer_Filter_Attribute';
    }

    /**
     * Set attribute model
     *
     * @return Enterprise_Search_Block_Catalogsearch_Layer_Filter_Attribute
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Block_Catalogsearch_Layer_Filter_Attribute
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

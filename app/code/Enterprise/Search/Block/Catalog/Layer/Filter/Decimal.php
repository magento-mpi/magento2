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
 * Catalog Layer Decimal Attribute Filter Block
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalog_Layer_Filter_Decimal extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Initialize Decimal Filter Model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Enterprise_Search_Model_Catalog_Layer_Filter_Decimal';
    }

    /**
     * Prepare filter process
     *
     * @return Magento_Catalog_Block_Layer_Filter_Decimal
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Block_Catalog_Layer_Filter_Decimal
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

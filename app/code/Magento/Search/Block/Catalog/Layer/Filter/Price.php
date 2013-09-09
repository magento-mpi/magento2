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
 * Catalog layer price filter
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Block_Catalog_Layer_Filter_Price extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Initialize Price filter module
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Magento_Search_Model_Catalog_Layer_Filter_Price';
    }

    /**
     * Prepare filter process
     *
     * @return Magento_Catalog_Block_Layer_Filter_Price
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return Magento_Search_Block_Catalog_Layer_Filter_Price
     */
    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Layer Decimal Attribute Filter Block
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Layer_Filter_Decimal extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Initialize Decimal Filter Model
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_filterModelName = 'Mage_Catalog_Model_Layer_Filter_Decimal';
    }

    /**
     * Prepare filter process
     *
     * @return Mage_Catalog_Block_Layer_Filter_Decimal
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}

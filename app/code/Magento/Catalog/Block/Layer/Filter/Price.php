<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog layer price filter
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Layer_Filter_Price extends Magento_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Initialize Price filter module
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_filterModelName = 'Magento_Catalog_Model_Layer_Filter_Price';
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
}

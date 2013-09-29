<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product\Grouped;

class AssociatedProducts
    extends \Magento\Backend\Block\Catalog\Product\Tab\Container
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grouped_product_container');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Grouped Products');
    }

    /**
     * Get parent tab code
     *
     * @return string
     */
    public function getParentTab()
    {
        return 'product-details';
    }
}

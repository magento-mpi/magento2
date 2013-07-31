<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle selection product block
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search extends Magento_Adminhtml_Block_Widget
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/bundle/option/search.phtml';

    protected function _construct()
    {
        $this->setId('bundle_option_selection_search');
    }

    /**
     * Create search grid
     *
     * @return Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid',
                'adminhtml.catalog.product.edit.tab.bundle.option.search.grid'
            )
        );
        return parent::_prepareLayout();
    }

    /**
     * Prepare search grid
     *
     * @return Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search
     */
    protected function _beforeToHtml()
    {
        $this->getChildBlock('grid')->setIndex($this->getIndex())
            ->setFirstShow($this->getFirstShow());
        return parent::_beforeToHtml();
    }
}

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

class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search extends Mage_Adminhtml_Block_Widget
{

    protected $_template = 'product/edit/bundle/option/search.phtml';

    protected function _construct()
    {
        $this->setId('bundle_option_selection_search');

    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Bundle_Helper_Data')->__('Please Select Products to Add');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid',
                'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
        );
        return parent::_prepareLayout();
    }

    protected function _beforeToHtml()
    {
        $this->getChildBlock('grid')->setIndex($this->getIndex())
            ->setFirstShow($this->getFirstShow());

        return parent::_beforeToHtml();
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'id'    => 'add_button_' . $this->getIndex(),
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Add Selected Product(s) to Option'),
            'onclick' => 'bSelection.productGridAddSelected(event)',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}

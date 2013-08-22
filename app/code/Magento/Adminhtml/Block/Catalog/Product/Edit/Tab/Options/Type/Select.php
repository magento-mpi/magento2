<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * customers defined options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Select extends
    Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{

    protected $_template = 'catalog/product/edit/options/type/select.phtml';

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    protected function _prepareLayout()
    {
        $this->addChild('add_select_row_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Add New Row'),
            'class' => 'add add-select-row',
            'id'    => 'product_option_${option_id}_add_select_row'
        ));

        $this->addChild('delete_select_row_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Delete Row'),
            'class' => 'delete delete-select-row icon-btn',
            'id'    => 'product_option_${id}_select_${select_id}_delete'
        ));

        return parent::_prepareLayout();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_select_row_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_select_row_button');
    }

    public function getPriceTypeSelectHtml()
    {
        $this->getChildBlock('option_price_type')
            ->setData('id', 'product_option_${id}_select_${select_id}_price_type')
            ->setName('product[options][${id}][values][${select_id}][price_type]');

        return parent::getPriceTypeSelectHtml();
    }
}

<?php
/**
 * admin product edit tabs
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('product_info_tabs');
        $this->setDestElementId('product_edit_form');
        $this->setTitle(__('Product Information'));
    }

    protected function _initChildren()
    {
        $this->addTab('attributes', array(
            'label'     => __('Attributes'),
            'content'   => 'info',
            'active'    => true
        ));

        $this->addTab('price', array(
            'label'     => __('Price'),
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price')->toHtml(),
        ));

        $this->addTab('images', array(
            'label'     => __('Images'),
            'content'   => 'images',
        ));

        $this->addTab('categories', array(
            'label'     => __('Categories'),
            'content'   => 'Categories',
        ));

        $this->addTab('stores', array(
            'label'     => __('Stores'),
            'content'   => 'stores',
        ));

        $this->addTab('superproduct', array(
            'label'     => __('Super Product'),
            'content'   => 'Super Product',
        ));

    }
}

<?php
/**
 * Adminhtml customer grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
    }

    public function getCollection()
    {
        if (empty($this->_collection)) {
            $this->_collection = Mage::getResourceModel('catalog/product_collection');
            if ($this->getCategoryId()) {
                $this->_collection->addCategoryFilter($this->getCategoryId());
            }
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        $this->addColumn('id', array('header'=>__('id'), 'width'=>5, 'align'=>'center', 'sortable'=>false, 'index'=>'product_id'));
        $this->addColumn('sku', array('header'=>__('sku'), 'width'=>40, 'align'=>'center', 'index'=>'sku'));
        $this->addColumn('name', array('header'=>__('name'), 'index'=>'name'));

        return parent::_beforeToHtml();
    }
}
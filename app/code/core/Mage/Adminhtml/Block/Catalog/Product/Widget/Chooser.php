<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product chooser for Wysiwyg CMS widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $jsObject = $this->getJsChooserObject();
        $this->setRowClickCallback("$jsObject.clickProduct.bind($jsObject)");
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
        $chooserId = $element->getId() . 'product_chooser';
        $jsObject = 'oProduct' . $chooserId;
        $html = '
            <a href="javascript:void(0)" id="'.$chooserId.'" class="widget-option-chooser"><img src="'.$image.'" title="'.$this->helper('catalog')->__('Open Chooser').'" /></a>
            <script type="text/javascript">
                '.$jsObject.' = new WysiwygWidget.optionProduct("'.$jsObject.'", "'.$this->getGridUrl().'");
                Event.observe("'.$chooserId.'", "click", '.$jsObject.'.choose.bind('.$jsObject.'));
            </script>
        ';
        $element->setData('after_element_html',$html);
        return $element;
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $selected = $this->_getSelectedProducts();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
            	$this->getCollection()->addFieldToFilter('sku', array('in'=>$selected));
            } else {
            	$this->getCollection()->addFieldToFilter('sku', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(0)
        	->addAttributeToSelect('name')
            ->addAttributeToFilter('type_id', array('in'=>array(
                Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            )));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));

        $this->addColumn('chooser_sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product_widget/chooser', array('_current' => true));
    }

    protected function _getSelectedProducts()
    {
        return $this->getRequest()->getPost('selected', array());
    }
}


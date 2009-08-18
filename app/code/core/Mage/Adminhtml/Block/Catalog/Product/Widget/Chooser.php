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
 * Product Chooser for "Product Link" Cms Widget Plugin
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = $element->getId() . md5(microtime());
        $sourceUrl = $this->getUrl('*/catalog_product_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('adminhtml/cms_page_edit_wysiwyg_widget_chooser')
            ->setElement($element)
            ->setSourceUrl($sourceUrl);

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $productId = isset($value[1]) ? $value[1] : false;
            $categoryId = isset($value[2]) ? $value[2] : false;
            $label = '';
            if ($categoryId) {
                $label = Mage::getSingleton('catalog/category')->load($categoryId)->getName() . ' / ' . $label;
            }
            if ($productId) {
                $label .= Mage::getSingleton('catalog/product')->load($productId)->getName();
            }
            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");

                var productId = trElement.down("td").innerHTML;
                var productName = trElement.down("td").next().next().innerHTML;
                var chooser = $(grid.containerId).up().previous("a.widget-option-chooser");

                var optionLabel = productName;
                var optionValue = "product/" + productId;
                if (grid.categoryId) {
                    optionValue += "/" + grid.categoryId;
                }
                if (grid.categoryName) {
                    optionLabel = grid.categoryName + " / " + optionLabel;
                }

                chooser.previous("input.widget-option").value = optionValue;
                chooser.next("label.widget-option-label").update(optionLabel);

                var responseContainerId = "responseCnt" + chooser.id;
                $(responseContainerId).hide();
            }
        ';
        return $js;
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getCategoryClickListenerJs()
    {
        $js = '
            function (node, e) {
                {jsObject}.addVarToUrl("category_id", node.attributes.id);
                {jsObject}.reload({jsObject}.url);
                {jsObject}.categoryId = node.attributes.id;
                {jsObject}.categoryName = node.text;
            }
        ';
        $js = str_replace('{jsObject}', $this->getJsObjectName(), $js);
        return $js;
    }

    /**
     * Prepare products collection, defined collection filters (category, product type)
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(0)
        	->addAttributeToSelect('name')
            ->addAttributeToFilter('type_id', array('in'=>array(
                Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
                Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            )));

        if ($categoryId = $this->getCategoryId()) {
            $productIds = Mage::getModel('catalog/category')->load($categoryId)
                ->getProductsPosition();
            $productIds = array_keys($productIds);
            if (empty($productIds)) {
                $productIds = 0;
            }
            $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for products grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
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

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product_widget/chooser', array('products_grid' => true, '_current' => true));
    }
}

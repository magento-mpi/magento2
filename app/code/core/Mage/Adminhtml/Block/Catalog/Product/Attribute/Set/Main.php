<?php
/**
 * @package     Mage
 * @subpackage  Admihtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/main.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('group_tree',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_tree_group')
        );

        $this->setChild('attribute_tree',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_tree_attribute')
        );

        $this->setChild('new_set_form',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset')
        );

        $this->setChild('new_group_form',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formgroup')
        );

        $this->setChild('new_attribute_form',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formattribute')
        );

        $this->setChild('sets_filter',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_toolbar_main_filter')
        );
    }

    public function getGroupTreeHtml()
    {
        return $this->getChildHtml('group_tree');
    }

    public function getAttributeTreeHtml()
    {
        return $this->getChildHtml('attribute_tree');
    }

    public function getSetFormHtml()
    {
        return $this->getChildHtml('new_set_form');
    }

    public function getGroupFormHtml()
    {
        return $this->getChildHtml('new_group_form');
    }

    public function getAttributeFormHtml()
    {
        return $this->getChildHtml('new_attribute_form');
    }

    public function getSetsFilterHtml()
    {
        return $this->getChildHtml('sets_filter');
    }
}
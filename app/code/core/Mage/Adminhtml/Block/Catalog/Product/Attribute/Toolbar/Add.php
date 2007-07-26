<?php
/**
 * Attribute add/edit form toolbar
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Toolbar_Add extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/attribute/add.phtml');
    }

    protected function _getHeader()
    {
        return __("Product Attributes");
    }

    protected function _initChildren()
    {
        $this->setChild('new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Add New Attribute'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/edit').'\'',
                    'class' => 'add'
                ))
        );

    }

    public function getNewButtonHtml()
    {
        return $this->getChildHtml('new_button');
    }
}
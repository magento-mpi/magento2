<?php
/**
 * Catalog manage products block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/product.phtml');
    }
    
    protected function _initChildren()
    {
        $this->setChild('add_new_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Add Product'),
                    'onclick'   => "setLocation('".Mage::getUrl('*/*/new')."')",
                    'class'   => 'add'
					))
				);
				
        $this->setChild('store_switcher', 
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setSwitchUrl(Mage::getUrl('*/*/*', array('store'=>null)))
        );
				
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/catalog_product_grid', 'product.grid'));
    }
    
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }
    
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
    
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }
}

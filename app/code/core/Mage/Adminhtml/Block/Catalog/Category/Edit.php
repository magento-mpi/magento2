<?php
/**
 * Category edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Edit extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/category/edit.phtml');
    }
    
    protected function _initChildren()
    {
        $this->setChild('tabs',
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );
        
        $this->setChild('save_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Category'),
                    'onclick'   => 'categoryForm.submit()',
                    'class' => 'save'
                ))
        );
        
        $this->setChild('delete_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Category'),
                    'onclick'   => 'categoryDelete()',
                    'class' => 'delete'
                ))
        );

        $this->setChild('reset_button', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => "setLocation('".Mage::getUrl('*/*/*', array('_current'=>true))."')"
                ))
        );
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }
    
    public function getCategoryId()
    {
        return Mage::registry('category')->getId();
    }
    
    public function getCategoryName()
    {
        return Mage::registry('category')->getName();
    }
    
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }
    
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }
    
    public function getHeader()
    {
        return $this->getCategoryId() ? $this->getCategoryName() : __('New Category');
    }
    
    public function getDeleteUrl()
    {
        return Mage::getUrl('*/*/delete', array('_current'=>true));
    }
    
    public function getProductIdsString()
    {
        $ids = Mage::registry('category')->getProductIds();
        if (is_array($ids)) {
            return implode(',', $ids);
        }
        return '';
    }
}

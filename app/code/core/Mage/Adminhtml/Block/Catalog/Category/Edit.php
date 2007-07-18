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
        $this->setTemplate('adminhtml/catalog/category/edit.phtml');
    }
    
    protected function _initChildren()
    {
        $this->setChild('tabs',
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );
        
        $this->setChild('saveButton', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save customer'),
                    'onclick'   => 'categoryForm.submit()'
                ))
        );
        
        $this->setChild('deleteButton', 
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete customer'),
                    'onclick'   => 'categoryDelete()'
                ))
        );

    }
    
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }
    
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }
    
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }
    
    public function getHeader()
    {
        return __('New Category');
    }
}

<?php
/**
 * Catalog product controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        //$this->_addBreadcrumb(__('Catalog'), __('catalog title'));
        
        $this->renderLayout();
    }
    
    public function gridAction()
    {
        
    }
    
    public function editAction()
    {
        
    }
    
    public function saveAction()
    {
        
    }
    
    public function deleteAction()
    {
        
    }
    
    public function testAction()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('price')
            ->load();
        
    }
}

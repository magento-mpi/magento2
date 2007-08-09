<?php
/**
 * Category controller
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_CategoryController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Category view
     */
    public function viewAction()
    {
        $category = Mage::getModel('catalog/category')
            ->load($this->getRequest()->getParam('id', false));
        
        if (!$this->_canShowCategory($category)) {
            $this->_forward('noRoute');
            return;
        }
        
        Mage::register('current_category', $category);
        $this->loadLayout(null, '', false);
        
        $this->getLayout()->loadUpdateFile(
            Mage::getDesign()->getLayoutFilename($category->getLayoutUpdateFileName())
        );
        $this->getLayout()->generateBlocks();
        $this->renderLayout();
    }
    
    protected function _canShowCategory($category)
    {
        if (!$category->getIsActive()) {
            return false;
        }
        
        $rootCategory = Mage::getModel('catalog/category')
            ->load(Mage::getSingleton('core/store')->getConfig('catalog/category/root_id'));
            
        if (!in_array($category->getId(), explode(',', $rootCategory->getAllChildren()))) {
            return false;
        }
        return true;
    }
}

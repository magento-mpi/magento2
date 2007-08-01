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
    function viewAction()
    {
        $category = Mage::getSingleton('catalog/category')
            ->load($this->getRequest()->getParam('id', false));
        
        if (!$category->getIsActive()) {
            $this->_forward('noRoute');
            return;
        }
        
        Mage::register('current_category', $category);
        $this->loadLayout(null, '', false);

        if ($category->getCustomLayout()) {
            $this->getLayout()->loadString($category->getCustomLayout());
        } else {
            $this->getLayout()->loadUpdateFile(Mage::getDesign()->getLayoutFilename('catalog/defaultCategoryLevel1.xml'));
        }
        
        $this->getLayout()->generateBlocks();
        $this->renderLayout();

    }
}

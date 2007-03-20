<?php


class Mage_Catalog_ProductController extends Mage_Core_Controller_Admin_Action 
{
    /**
     * Product card structure (json)
     *
     */
    public function cardAction()
    {
        $card = Mage::createBlock('catalog_product_card', 'product_card');
        $this->getResponse()->setBody($card->toJson());
    }
    
    public function formAction()
    {
        $form = Mage::createBlock('catalog_product_form', 'product_form');
        $this->getResponse()->setBody($form->toString());
        
        //echo "group: $groupId, set: $setId";
    }
    
    /**
     * GRid 
     *
     */
    public function gridAction() 
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/product/initGridLayout.json', 'mage_catalog');
    }
    
    /**
     * Product collection JSON
     *
     */
    public function gridDataAction()
    {
        $pageSize = isset($_POST['limit']) ? $_POST['limit'] : 30;
        $prodCollection = Mage::getModel('catalog','product_collection');
        
        $prodCollection->addAttributeToSelect('name', 'varchar');
        $prodCollection->addAttributeToSelect('price', 'decimal');
        $prodCollection->addAttributeToSelect('description', 'text');
        
        $prodCollection->setPageSize($pageSize);
        
        if ($categoryId = $this->getRequest()->getParam('category')) {
            
            $tree = Mage::getModel('catalog','Category_Tree');
            $data = $tree->getLevel($categoryId, 0);
            
            if (empty($data)) {
                $arrCategories = array($categoryId);
            }
            else {
                $arrCategories = array();
                $prodCollection->distinct(true);
                foreach ($data as $node) {
            		$arrCategories[] = $node->getId();
            	}
            }
        	$prodCollection->addCategoryFilter($arrCategories);
        }
        
        
        
        $page = isset($_POST['start']) ? $_POST['start']/$pageSize+1 : 1;
        
        $order = isset($_POST['sort']) ? $_POST['sort'] : 'product_id';
        $dir   = isset($_POST['dir']) ? $_POST['dir'] : 'desc';
        $prodCollection->setOrder($order, $dir);
        $prodCollection->setCurPage($page);
        $prodCollection->load();
        
        $arrGridFields = array('product_id', 'name', 'price', 'description');
        
        $this->getResponse()->setBody(Zend_Json::encode($prodCollection->__toArray($arrGridFields)));
    }
    
    /**
     * Save product attributes
     *
     */
    public function saveAttributesAction() 
    {
        
    }
}

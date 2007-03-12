<?php




class Mage_Catalog_TreeController extends Mage_Core_Controller_Admin_Action 
{

    /**
     * Index action
     *
     * Display catalog tree
     *
     */
    function indexAction() 
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/catalogTree.json', 'mage_catalog');
    }
    
    function categoriesAction() 
    {
        $tree = Mage::getModel('catalog','category_tree');

        $data = $tree->getLevel($this->getRequest()->getPost('node',1));
        //$data = $tree->getLevel(1);

        $json = array();
        foreach ($data as $node) {
            $tmp = array();
            $tmp['text'] = $node->getData('attribute_value').':'.$node->getId();
            $tmp['id'] = $node->getId();
            $tmp['cls'] = 'folder';
            if (!$node->isParent()) {
                $tmp['leaf'] = 'true';    
            }
            $json[] = $tmp;            
        }
        unset($tmp);
        $this->getResponse()->setBody(Zend_Json::encode($json));
    }
    
    
    function recentProductsAction()
    {
        // TODO: create system storage
        if (!is_array($_SESSION['OPEN_PRODUCTS'])) {
            $_SESSION['OPEN_PRODUCTS'] = array();
        }
        
        $json = array();
        
        foreach ($_SESSION['OPEN_PRODUCTS'] as $productId) {
            $json[] = array(
                       'text'=>'Product #'.$productId, 
                       'id'=>'recent-product-'.$productId, 
                       'cls'=>'product', 
                       'leaf'=>'true'
                      );
        }

        $this->getResponse()->setBody(json_encode($json));
    }
    
    function recentSearchesAction()
    {
        
    }
    
    function savedSearchesAction()
    {
        
    }
    
    function __call($method, $args) 
    {
        var_dump($method);
    }
}

<?php


class Mage_Catalog_ProductController extends Mage_Core_Controller_Admin_Action 
{
    public function gridAction() 
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/product/initGridLayout.json', 'mage_catalog');
    }
    
    public function gridDataAction()
    {
        $prodCollection = Mage::getModel('catalog','product_collection');
        $prodCollection->addAttributeToSelect('name', 'varchar');
        $prodCollection->addAttributeToSelect('price', 'decimal');
        $prodCollection->addAttributeToSelect('description', 'text');
        
        $prodCollection->setPageSize(20);
        //$prodCollection->addFilter('website_id', Mage::getCurentWebsite(), 'and');
        
        /*
        if ($categoryId = $this->getRequest()->getParam('category')) {
            $arrCategories = array($categoryId);
            $tree = Mage::getModel('catalog','Categories');
            $data = $tree->getLevel($categoryId, 0);
        	foreach ($data as $node) {
        		$arrCategories[] = $node->getId();
        	}
        	
        	$condition = Mage::getModel('catalog')->getReadConnection()->quoteInto('category_id IN (?)', $arrCategories);
        	//$prodCollection->addFilter('category', $condition, 'string');
        }*/
        $prodCollection->addCategoryFilter(10);
        
        
        //$page = isset($_POST['start']) ? $_POST['start']/20+1 : 1;
        /*
        $order = isset($_POST['sort']) ? $_POST['sort'] : 'product_id';
        $dir   = isset($_POST['dir']) ? $_POST['dir'] : 'desc';
        $prodCollection->setOrder($order, $dir);
        $prodCollection->setCurPage($page);*/
        $prodCollection->load();
        
        $arrGridFields = array('product_id', 'name', 'price', 'description');
        $this->getResponse()->setBody(Zend_Json::encode($prodCollection->__toArray($arrGridFields)));
    }

    public function viewAction() 
    {
        $productId = $this->getRequest()->getParam('product');
        // TODO:  save product
        if (!isset($_SESSION['OPEN_PRODUCTS'])) {
        	$_SESSION['OPEN_PRODUCTS'] = array();
        }
        if (!in_array($productId, $_SESSION['OPEN_PRODUCTS'])) {
        	$_SESSION['OPEN_PRODUCTS'][] = $productId;
        }

        $productFormBlock = Mage::createBlock('form', 'form.product');
        $productFormBlock->setViewName('Mage_Core', 'form');
        $productFormBlock->setAttribute('legend', 'Test form');
        
        $productFormBlock->addField('product_id', 'hidden', array('name'=>'product_id', 'value'=>"as\"'''2"));
        $productFormBlock->addField('category_id', 'hidden', array('name'=>'category_id', 'value'=>11));
        $productFormBlock->addField('text1', 'text', array('name'=>'text1', 'id'=>'text1', 'value'=>11, 'label'=>'My field'));
        $productFormBlock->addField('text2', 'textarea', array('name'=>'text2', 'id'=>'text2', 'value'=>11));
        $productFormBlock->addField('text3', 'select', array('name'=>'text3', 'id'=>'text3', 'value'=>11, 'values'=>array(0=>array('value'=>1, 'label'=>'1111111'))));
        $productFormBlock->addField('text4', 'button', array('name'=>'text4', 'id'=>'text4', 'value'=>11));
        $productFormBlock->addField('text5', 'submit', array('name'=>'text5', 'id'=>'text5', 'value'=>11));
        $productFormBlock->addField('text6', 'radio', array('name'=>'text6', 'id'=>'text6', 'value'=>11));
        $productFormBlock->addField('text7', 'checkbox', array('name'=>'text7', 'id'=>'text7', 'value'=>11));
        $productFormBlock->addField('text8', 'password', array('name'=>'text8', 'id'=>'text8', 'value'=>11));
        $productFormBlock->addField('text9', 'file', array('name'=>'text8', 'id'=>'text8', 'value'=>11));
        $productFormBlock->addField('text10', 'image', array('name'=>'text8', 'id'=>'text8', 'value'=>11));
        $productFormBlock->addField('text11', 'button', array('name'=>'text8', 'id'=>'text8', 'value'=>11));
        $productFormBlock->addField('text12', 'note', array('name'=>'text8', 'id'=>'text8', 'value'=>11));
        
        $this->getResponse()->setBody($productFormBlock->toString());
    }
    
    public function saveAction() 
    {
        echo 'P save';
    }
}

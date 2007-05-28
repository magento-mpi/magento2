<?php
/*
 * ProductsController.php
 * -------------------------------------------------------------
 * @File:        CategoryController.php
 * @Last change: 2007-05-28 17:07:31
 * @Description: 
 * @Dependecies: <Dependencies>
 * 
 * @Author:      Alexander Stadnitski (hacki) :: vipalexdm@gmail.com
 * @URL:         http://www.hacki.te.ua/
 * @TODO:
 * -------------------------------------------------------------
 */
 
class Mage_Datafeed_ProductsController extends Mage_Core_Controller_Front_Action
{

    function IndexAction()
    {
        die("Products");
    }
    
    public function rssCategoryAction()
    {
        $categoryId = $this->getRequest()->getParam('category');
        $block = Mage::createBlock('tpl', 'export');
        $block->setTemplate('datafeed/rss20.phtml')
            ->assign('data', Mage::getModel('datafeed', 'export_catalog_product')->getCategoryProducts($categoryId));
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function textAction()
    {
        
    }
}
 
// ft:php
// fileformat:unix
// tabstop:4
?>

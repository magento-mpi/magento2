<?php
/*
 * ProductsController.php
 * -------------------------------------------------------------
 * @File:        CategoryController.php
 * @Last change: 2007-05-28 21:51:54
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

    public function IndexAction()
    {
        die("Products");
    }
    
    public function rssAction()
    {
        $categoryId = intval($this->getRequest()->getParam('category'));

        $category = Mage::getModel('catalog', 'category')
            ->load($categoryId);

        $channel = new Varien_Object();
        $channel->setTitle( $category->getData('name') );
        $channel->setCategoryId( $category->getData('category_id') );
        $channel->setDescription( $category->getData('description') );

        $block = $this->getLayout()->createBlock('tpl', 'export');
        $block->setTemplate('datafeed/Product/rss20.phtml')
            ->assign('data', Mage::getModel('datafeed', 'export_catalog_product')->getCategoryProducts($categoryId));

        $block->assign('channel_data', $channel);

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

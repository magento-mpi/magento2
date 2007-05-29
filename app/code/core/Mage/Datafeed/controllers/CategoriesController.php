<?php
/*
 * CategoryController.php
 * -------------------------------------------------------------
 * @File:        CategoryController.php
 * @Last change: 2007-05-29 16:26:09
 * @Description: 
 * @Dependecies: <Dependencies>
 * 
 * @Author:      Alexander Stadnitski (hacki) :: vipalexdm@gmail.com
 * @URL:         http://www.hacki.te.ua/
 * @TODO:
 * -------------------------------------------------------------
 */
 
class Mage_Datafeed_CategoriesController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        die("Catergories");
    }

    public function rssAction()
    {
        $parentId = intval($this->getRequest()->getParam('category'));
        $parentId = ( $parentId >1 ) ? $parentId : 1;
        $channel = new Varien_Object();
        $channel->setTitle("Avaliable products categries");
        $channel->setDescription("Description of this channel");

        $block = $this->getLayout()->createBlock('tpl', 'export');
        $block->setTemplate('datafeed/Category/rss20.phtml')
            ->assign('data', Mage::getModel('datafeed', 'export_catalog_category')->getCategoriesList($parentId));

        $block->assign('channel_data', $channel);

        $this->getResponse()->setBody($block->toHtml());
    }

}
 
// ft:php
// fileformat:unix
// tabstop:4
?>

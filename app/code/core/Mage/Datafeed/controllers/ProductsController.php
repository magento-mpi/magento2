<?php
/**
 * Datafeed products controller
 *
 * @package     Mage
 * @subpackage  Datafeed
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
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

        $category = Mage::getModel('catalog/category')
            ->load($categoryId);

        $channel = new Varien_Object();
        $channel->setTitle( $category->getData('name') );
        $channel->setCategoryId( $category->getData('category_id') );
        $channel->setDescription( $category->getData('description') );

        $block = $this->getLayout()->createBlock('core/template', 'export');
        $block->setTemplate('datafeed/Product/rss20.phtml')
            ->assign('data', Mage::getModel('datafeed/export_catalog_product')->getCategoryProducts($categoryId));

        $block->assign('channel_data', $channel);

        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function csvAction()
    {
        header("Content-type: text/plain");
        #header("Content-type: text/csv");
        $model = Mage::getModel('datafeed/export_catalog_product');
        $categoryId = intval($this->getRequest()->getParam('category'));
         
        $category = Mage::getModel('catalog/category')
            ->load($categoryId);

        $block = $this->getLayout()->createBlock('core/template', 'export');

        $data = $model->getCategoryProducts($categoryId); 
        $data->each(Array($model, "formatCSV"), $data);

        $block->setTemplate('datafeed/Product/csv.phtml')
            ->assign('data', $data);

        $this->getResponse()->setBody($block->toHtml());
    }

}
 
// ft:php
// fileformat:unix
// tabstop:4
?>

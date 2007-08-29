<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Datafeed
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Datafeed products controller
 *
 * @category   Mage
 * @package    Mage_Datafeed
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

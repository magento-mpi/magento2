<?php
/**
 * Datafeed categories controller
 *
 * @package     Mage
 * @subpackage  Datafeed
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
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

    public function csvAction()
    {
        header("Content-type: text/plain");
        $parentId = intval($this->getRequest()->getParam('category'));
        $parentId = ( $parentId >1 ) ? $parentId : 1;

        $block = $this->getLayout()->createBlock('tpl', 'export');
        $block->setTemplate('datafeed/Category/csv.phtml')
            ->assign('data', Mage::getModel('datafeed', 'export_catalog_category')->getCategoriesList($parentId));

        $this->getResponse()->setBody($block->toHtml());
    }

}
 
// ft:php
// fileformat:unix
// tabstop:4
?>

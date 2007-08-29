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
 * Datafeed categories controller
 *
 * @category   Mage
 * @package    Mage_Datafeed
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

        $block = $this->getLayout()->createBlock('core/template', 'export');
        $block->setTemplate('datafeed/Category/rss20.phtml')
            ->assign('data', Mage::getModel('datafeed/export_catalog_category')->getCategoriesList($parentId));

        $block->assign('channel_data', $channel);

        $this->getResponse()->setBody($block->toHtml());
    }

    public function csvAction()
    {
        header("Content-type: text/plain");
        $parentId = intval($this->getRequest()->getParam('category'));
        $parentId = ( $parentId >1 ) ? $parentId : 1;

        $block = $this->getLayout()->createBlock('core/template', 'export');
        $block->setTemplate('datafeed/Category/csv.phtml')
            ->assign('data', Mage::getModel('datafeed/export_catalog_category')->getCategoriesList($parentId));

        $this->getResponse()->setBody($block->toHtml());
    }

}
 
// ft:php
// fileformat:unix
// tabstop:4
?>

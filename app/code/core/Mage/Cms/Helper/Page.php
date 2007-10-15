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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Cms page helper
 *
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Cms_Helper_Page extends Mage_Core_Helper_Abstract
{
    /**
    * Renders CMS page
    * 
    * Call from controller action
    * 
    * @param Mage_Core_Controller_Front_Action $action
    * @param integer $pageId
    * @return boolean
    */
    public function renderPage(Mage_Core_Controller_Front_Action $action, $pageId=null)
    {
        $page = Mage::getSingleton('cms/page');
        $page->setStoreId(Mage::app()->getStore()->getId());
        
        if (is_null($pageId)) {
            $pageId = $action->getRequest()->getParam('page_id', false);
        }
        if ($pageId) {
            $page->load($pageId);
        }
        if (!$page->getId()) {
            return false;
        }

        $action->loadLayout();

        if ($root = $action->getLayout()->getBlock('root')) {
            $template = (string)Mage::getConfig()->getNode('global/cms/layouts/'.$page->getRootTemplate().'/template');
            $root->setTemplate($template);
        }

        if ($content = $action->getLayout()->getBlock('content')) {
            $block = $action->getLayout()->createBlock('cms/page')->setPage($page);
            $content->append($block);
        }

        $action->renderLayout();
        
        return true;
    }   
}
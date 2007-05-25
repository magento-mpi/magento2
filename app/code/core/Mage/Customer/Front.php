<?php
/**
 * Customer front
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Front
{
    public static function beforeRenderLayout()
    {
        $layout = Mage::registry('action')->getLayout();
        $topLinks = $layout->getBlock('top.links');
        if(!$topLinks){
            return false;
        }
        $topLinks->append($layout->createBlock('list_link', 'top.links.wishlist')
            ->setLink('', 'href="'.Mage::getUrl('customer', array('controller'=>'wishlist')).'"', 'Wishlist', ''));

        // Add logout link
        $custSession = Mage::getSingleton('customer', 'session');
        if ($custSession->isLoggedIn()) {
            if ($topLinks) {
                $topLinks->append($layout->createBlock('list_link', 'top.links.logout')
                    ->setLink('', 'href="'.Mage::getUrl('customer', array('controller'=>'account', 'action'=>'logout')).'"', 'Logout', ''));
            }
            
            $topMenu = $layout->getBlock('top.menu');
            if ($topMenu) {
                $topMenu->insert($layout->createBlock('tag', 'top.menu.welcome')
                    ->setTagName('strong')
                    ->setContents('Welcome, ' . $custSession->getCustomer()->getName()));
            }
        }
    }
    
    public static function beforeFrontRun()
    {
        $custSession = Mage::getSingleton('customer', 'session');
        if ($custSession->isLoggedIn()) {
            Mage::getSingleton('core', 'session_visitor')->setCustomerId($custSession->getCustomer()->getCustomerId());
        }
    }
} 
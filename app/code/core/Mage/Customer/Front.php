<?php
/**
 * Customer front
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Front
{
    public static function beforeRenderLayout()
    {
        // Add logout link
        if (Mage::getSingleton('customer', 'session')->isLoggedIn()) {
            $topLinks = Mage::getBlock('top.links');
            if (!$topLinks) {
                return;
            }
            $topLinks
                ->append(Mage::createBlock('list_link', 'top.links.logout')
                    ->setLink('', 'href="'.Mage::getUrl('customer', array('controller'=>'account', 'action'=>'logout')).'"', 'Logout', ''))
                ->insert(Mage::createBlock('tag', 'top.links.welcome')
                    ->setTagName('strong')
                    ->setContents('Welcome, ' . Mage::getSingleton('customer', 'session')->getCustomer()->getName()));
        }
    }
}
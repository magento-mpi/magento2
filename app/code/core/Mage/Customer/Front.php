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
    public static function action_postDispatch()
    {
        // Add logout link
        if (Mage::getSingleton('customer_model', 'session')->isLoggedIn()) {
            Mage::getBlock('top.links')->append(
                Mage::createBlock('list_link', 'top.links.logout')->setLink(
                    '', 'href="'.Mage::getBaseUrl('','Mage_Customer').'/account/logout/"', 'Logout', ''
                )
            );
        }
    }
}
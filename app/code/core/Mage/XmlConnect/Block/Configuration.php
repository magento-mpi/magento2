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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application configuration renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Configuration extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $app = Mage::registry('current_app');
        $conf = array();
        if ($app) {
            $conf = $app->prepareConfiguration();
        }

        $xml = new Varien_Simplexml_Element('<content></content>');

        $section = $xml->addChild('navigationBar');
        $section->addChild('tintColor', '#rrggbbaa'); // FIXME
        $section->addChild('backgroundColor', '#rrggbbaa'); // FIXME
        $section->addChild('icon', 'http://blah-blah/smallIcon.png'); // FIXME
        $font = $section->addChild('font');
            $font->addAttribute('name', 'Arial'); // FIXME
            $font->addAttribute('size', '12.0'); // FIXME
            $font->addAttribute('color', '#rrggbbaa'); // FIXME

        $section = $xml->addChild('tabBar');
        $section->addChild('tintColor', '#rrggbbaa'); // FIXME
        $section->addChild('backgroundColor', '#rrggbbaa'); // FIXME
        $font = $section->addChild('font');
            $font->addAttribute('name', 'Arial'); // FIXME
            $font->addAttribute('size', '12.0'); // FIXME
            $font->addAttribute('color', '#rrggbbaa'); // FIXME
        $tab = $section->addChild('home');
            $tab->addAttribute('icon', 'http://blah-blah/home.png'); // FIXME
            $tab->addAttribute('title', 'Home'); // FIXME
        $tab = $section->addChild('shop');
            $tab->addAttribute('icon', 'http://blah-blah/browse.png'); // FIXME
            $tab->addAttribute('title', 'Shop'); // FIXME
        $tab = $section->addChild('search');
            $tab->addAttribute('icon', 'http://blah-blah/search.png'); // FIXME
            $tab->addAttribute('title', 'Search'); // FIXME
        $tab = $section->addChild('cart');
            $tab->addAttribute('icon', 'http://blah-blah/cart.png'); // FIXME
            $tab->addAttribute('title', 'Cart'); // FIXME
        $tab = $section->addChild('more');
            $tab->addAttribute('icon', 'http://blah-blah/more.png'); // FIXME
            $tab->addAttribute('title', 'More'); // FIXME

        $section = $xml->addChild('body');
        $section->addChild('backgroundColor', '#rrggbbaa'); // FIXME
        $section->addChild('scrollBackgroundColor', '#rrggbbaa'); // FIXME
        $section->addChild('itemBackgroundIcon', 'http://blah-blah/catBG.png'); // FIXME

        return $xml->asNiceXml();
    }
}

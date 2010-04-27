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
    protected $_conf = array();

    protected function _beforeToHtml()
    {
        $app = Mage::registry('current_app');
        if ($app) {
            $this->_conf = $app->prepareConfiguration();
        }
        //var_dump($this->_conf); die();
        return $this;
    }

    protected function _getConf($name, $defaultValue='') {
        if (isset($this->_conf[$name])) {
            return $this->_conf[$name];
        }
        return $defaultValue;
    }

    protected function _toHtml()
    {
        $xml = new Varien_Simplexml_Element('<content></content>');

        $section = $xml->addChild('navigationBar');
        $section->addChild('tintColor', '#rrggbbaa'); // FIXME
        $section->addChild('backgroundColor', $this->_getConf('color_header_background'));
        $section->addChild('icon', $this->_getConf('logo_header_image'));
        $font = $section->addChild('font');
            $font->addAttribute('name', $this->_getConf('font_header', 'ArialMT'));
            $font->addAttribute('size', $this->_getConf('font_header_size', '12.0'));
            $font->addAttribute('color', $this->_getConf('color_header'));

        $section = $xml->addChild('tabBar');
        $section->addChild('tintColor', '#rrggbbaa'); // FIXME
        $section->addChild('backgroundColor', '#rrggbbaa'); // FIXME
        $font = $section->addChild('font');
            $font->addAttribute('name', $this->_getConf('font_tabbar', 'ArialMT'));
            $font->addAttribute('size', $this->_getConf('font_tabbar_size', '12.0'));
            $font->addAttribute('color', '#rrggbbaa'); // FIXME
        $tab = $section->addChild('home');
            $tab->addAttribute('icon', $this->_getConf('tab_home_icon'));
            $tab->addAttribute('title', $this->_getConf('tab_home_label', 'Home'));
        $tab = $section->addChild('shop');
            $tab->addAttribute('icon', $this->_getConf('tab_shop_icon'));
            $tab->addAttribute('title', $this->_getConf('tab_shop_label', 'Shop'));
        $tab = $section->addChild('search');
            $tab->addAttribute('icon', $this->_getConf('tab_search_icon'));
            $tab->addAttribute('title', $this->_getConf('tab_search_label', 'Search'));
        $tab = $section->addChild('cart');
            $tab->addAttribute('icon', $this->_getConf('tab_cart_icon'));
            $tab->addAttribute('title', $this->_getConf('tab_cart_label', 'Cart'));
        $tab = $section->addChild('more');
            $tab->addAttribute('icon', $this->_getConf('tab_more_icon'));
            $tab->addAttribute('title', $this->_getConf('tab_more_label', 'More'));

        $section = $xml->addChild('body');
        $section->addChild('backgroundColor', $this->_getConf('color_body'));
        $section->addChild('scrollBackgroundColor', '#rrggbbaa'); // FIXME
        $section->addChild('itemBackgroundIcon', 'http://blah-blah/catBG.png'); // FIXME

        return $xml->asNiceXml();
    }
}

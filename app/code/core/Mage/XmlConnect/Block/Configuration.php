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
    protected $_app;

    protected function _beforeToHtml()
    {
        $app = Mage::registry('current_app');
        if ($app) {
            $this->_app = $app;
        } else {
            $this->_app = Mage::getModel('xmlconnect/application');
            $this->_app->loadDefaultConfiguration();
        }
        return $this;
    }

    protected function _getConf($path) {
        if( substr($path, -5) == '/icon' ) {
            $url = $this->_app['conf/'.$path];
            if (strpos($url, '://') === FALSE ) {
                $url = Mage::getBaseUrl('media').'xmlconnect/'.$url;
            }
            return $url;
        }
        return $this->_app['conf/'.$path];
    }

    protected function _toHtml()
    {
        $xml = new Varien_Simplexml_Element('<configuration></configuration>');
            $section = $xml->addChild('navigationBar');
                $section->addChild('tintColor', $this->_getConf('navigationBar/tintColor'));
                $section->addChild('backgroundColor', $this->_getConf('navigationBar/backgroundColor'));
                $section->addChild('icon', $this->_getConf('navigationBar/icon'));
                $font = $section->addChild('font');
                    $font->addAttribute('name', $this->_getConf('navigationBar/font/name'));
                    $font->addAttribute('size', $this->_getConf('navigationBar/font/size'));
                    $font->addAttribute('color', $this->_getConf('navigationBar/font/color'));
            $section = $xml->addChild('tabBar');
                $section->addChild('backgroundColor', $this->_getConf('tabBar/backgroundColor'));
                $tab = $section->addChild('home');
                    $tab->addAttribute('icon', $this->_getConf('tabBar/home/icon'));
                    $tab->addAttribute('title', $this->_getConf('tabBar/home/title'));
                $tab = $section->addChild('shop');
                    $tab->addAttribute('icon', $this->_getConf('tabBar/shop/icon'));
                    $tab->addAttribute('title', $this->_getConf('tabBar/shop/title'));
                $tab = $section->addChild('cart');
                    $tab->addAttribute('icon', $this->_getConf('tabBar/cart/icon'));
                    $tab->addAttribute('title', $this->_getConf('tabBar/cart/title'));
                $tab = $section->addChild('search');
                    $tab->addAttribute('icon', $this->_getConf('tabBar/search/icon'));
                    $tab->addAttribute('title', $this->_getConf('tabBar/search/title'));
                $tab = $section->addChild('more');
                    $tab->addAttribute('icon', $this->_getConf('tabBar/more/icon'));
                    $tab->addAttribute('title', $this->_getConf('tabBar/more/title'));
            $section = $xml->addChild('body');
                $section->addChild('backgroundColor', $this->_getConf('body/backgroundColor'));
                $section->addChild('scrollBackgroundColor', $this->_getConf('body/scrollBackgroundColor'));
                $section->addChild('itemBackgroundIcon', $this->_getConf('body/itemBackgroundIcon'));
        return $xml->asNiceXml();
    }
}

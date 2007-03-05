<?php

/**
 * Ecom Page Module
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:06:00 EET 2007
 */

class Ecom_Page_Setup extends Ecom_Core_Setup_Abstract 
{
    /**
     * Load Module
     * 
     * @param     none
     * @return	  none
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function loadFront()
    {
        Ecom::addObserver('controllerAction.noRoute', array($this, 'noRoute'));

        Ecom::addObserver('controllerAction.preDispatch', array($this, 'initLayout'));
        Ecom::addObserver('controllerAction.postDispatch', array($this, 'renderLayout'));
        
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }
    
    public function initLayout()
    {
        Ecom::dispatchEvent('initLayout.before');

        $rootLayout = array(':layout.init',
            array('+tpl', '#root', array('>setViewName', 'Ecom_Page', 'layout.2column'), array('>assign', 'imagesUrl', Ecom::getBaseUrl('skin').'/page/images'),
                array('>setChild', 'head', array('+list', '#head', 
                    array('>append', array('+tag', '#.title', array('>setTagName', 'title'), array('>setContents', 'Pepper Commerce'))),
                    array('>append', array('+tag_js', array('>setSrc', '/prototype.js'))),
                    array('>append', array('+tag_css', array('>setHref', '/page/style.css'))),
                )),
                array('>setChild', 'topLinks', array('+list', '#top.links')),
                array('>setChild', 'topMenu', array('+list', '#top.menu')),
                array('>setChild', 'topForms', array('+list', '#top.forms')),
                array('>setChild', 'left', array('+list', '#left')),
                array('>setChild', 'content', array('+list', '#content')),
                array('>setChild', 'bottomLinks', array('+list', '#bottom.links')),
            ),
        );

        Ecom_Core_Block::loadArray($rootLayout);

        Ecom::dispatchEvent('initLayout.after');
    }
    
    function updateLayout()
    {
        $baseUrl = Ecom::getBaseUrl();
        $moduleBaseUrl = $this->getModuleInfo()->getBaseUrl();

        $updateLayout = array(':page.layout.update',
            array('#top.links', array('>append', array('+list_link', '#.help', array('>setLink', '', 'href="'.$moduleBaseUrl.'/article/help"', 'Help')))),
            array('#top.menu', 
                array('>append', array('+list_link', '#.home', array('>setLink', 'id="nav-home"', 'href="'.$baseUrl.'" title="Home"', '<span>Home</span>'))),
                array('>append', array('+list_link', '#.preventcrime', array('>setLink', 'id="nav-help-prevent"', 'href="'.$baseUrl.'" title="Help Prevent Crime"', '<span>Help Prevent Crime</span>'))),
                array('>append', array('+list_link', '#.contactus', array('>setLink', 'id="nav-contact"', 'href="'.$baseUrl.'" title="Contact Us"', '<span>Contact Us</span>'))),
            ),
            array('#bottom.links',
                array('>append', array('+list_link', '#.home', array('>setLink', '', 'href="'.$baseUrl.'" title="Help"', 'Help'))),
                array('>append', array('+list_link', '#.sitemap', array('>setLink', '', 'href="'.$moduleBaseUrl.'/article/sitemap" title="Site Map"', 'Site Map'))),
                array('>append', array('+list_link', '#.privacy', array('>setLink', '', 'href="'.$moduleBaseUrl.'/article/privacy" title="Privacy Policy"', 'Privacy Policy'))),
            ),
        );
        
        Ecom_Core_Block::loadArray($updateLayout);
    }
    
    function renderLayout()
    {
        Ecom::getController()->getFront()->getResponse()->setBody(Ecom::getBlock('root')->toHtml());
    }
    
    function noRoute()
    {
        Ecom::getBlock('content')->append(Ecom::createBlock('text')->setText('<br>Page not found.'));
    }
}// Class Ecom_Page_Setup END
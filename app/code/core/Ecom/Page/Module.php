<?php

/**
 * Ecom Page Module
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Wed Feb 07 04:06:00 EET 2007
 */

class Ecom_Page_Module extends Ecom_Core_Module_Abstract 
{
    /**
     * Module info
     *
     * @var    array
     */
    protected $_info = array(
        'name'      => 'Ecom_Page',
        'version'   => '0.1.0a1',
    );
    
    /**
     * Load Module
     * 
     * @param     none
     * @return	  none
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function load()
    {
        Ecom::addObserver('controllerAction.noRoute', array($this, 'noRoute'));

        Ecom::addObserver('controllerAction.preDispatch', array($this, 'initLayout'));
        Ecom::addObserver('controllerAction.postDispatch', array($this, 'renderLayout'));
        
        Ecom::addObserver('initLayout.after', array($this, 'updateLayout'));
    }
    
    /**
     * Run module
     * 
     * @param     none
     * @return	  none
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    
    public function run()
    {
    	Ecom::dispatchEvent(__METHOD__);
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
/*
#include_once 'Zend/Json.php';

Ecom::setTimer('enc');
echo "<table border=1><tr><th>method</th><th>encode</th><th>decode</th></tr>";
for ($i=0; $i<1000; $i++) $json = json_encode($rootLayout);
echo "<tr><th>pecl_json</th><td>".Ecom::setTimer('enc');
for ($i=0; $i<1000; $i++) $test = json_decode($json);
echo "</td><td>".Ecom::setTimer('enc');
for ($i=0; $i<1000; $i++) $ser = serialize($rootLayout);
echo "</td></tr><tr><th>serialize</th><td>".Ecom::setTimer('enc');
for ($i=0; $i<1000; $i++) $test = unserialize($ser);
echo "</td><td>".Ecom::setTimer('enc');
for ($i=0; $i<1000; $i++) $json = Zend_Json::encode($rootLayout);
echo "</td></tr><tr><th>Zend_Json::encode</th><td>".Ecom::setTimer('enc');
for ($i=0; $i<1000; $i++) $test = Zend_Json::decode($json);
echo "</td><td>".Ecom::setTimer('enc');
echo "</td></tr></table>";
*/
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
}// Class Ecom_Page_Module END
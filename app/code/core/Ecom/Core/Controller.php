<?php

class Ecom_Core_Controller
{

    /**
     * Controller
     *
     * @var    Ecom_Core_Controller_Varien
     */
    static private $_controller;
    
    /**
     * Set default controller
     *
     * @param Ecom_Core_Controller_Zend $controller
     */
    public static function setController($controller)
    {
        self::$_controller = $controller;
        return self::$_controller;
    }

    /**
     * Get Controller
     *
     * @param     none
     * @return	  Ecom_Core_Controller_Zend
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    public static function getController()
    {
    	return self::$_controller;
    }
    
    public static function loadModuleConfig($modInfo)
    {
        self::getController()->loadModule($modInfo);
    }
    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($type='')
    {
        $url = self::getController()->getRequest()->getBaseUrl();

        switch ($type) {
            case 'skin':
                $url .= '/skins/default';
                break;

            case 'js':
                $url .= '/js';
                break;
        }

        return $url;
    }
}
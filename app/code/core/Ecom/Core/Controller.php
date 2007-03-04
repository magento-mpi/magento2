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
        self::$_controller  = $controller;
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

    public static function init()
    {
        #include_once 'Ecom/Core/Controller/Zend.php';
        Ecom_Core_Controller::setController(new Ecom_Core_Controller_Zend());
    }
    
    public static function initAdmin()
    {
        #include_once 'Ecom/Core/Controller/Zend.php';
        Ecom_Core_Controller::setController(new Ecom_Core_Controller_Zend_Admin());
    }
    
    public static function loadModuleConfig($modInfo)
    {
        self::getController()->loadModule($modInfo);
    }
}
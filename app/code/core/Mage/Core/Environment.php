<?php
/**
 * Core environment
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Environment
{
    /**
     * Get curent website id
     *
     * @return int
     */
    public static function getCurentWebsite()
    {
        return 1;
    }
    
    /**
     * Get curent user id
     *
     * @return int
     */
    public static function getCurentUser()
    {
        return 1;
    }
    
    /**
     * Get curent customer id
     *
     * @return int
     */
    public static function getCurentCustomer()
    {
        return 1;
    }
}
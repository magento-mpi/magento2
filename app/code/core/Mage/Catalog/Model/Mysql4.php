<?php
class Mage_Catalog_Model_Mysql4
{
    protected static $_read = null;
    protected static $_write = null;

    function __construct()
    {
        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');
    }

}
<?php
class Mage_Catalog_Model_Mysql4 extends Mage_Core_Model_Db
{
    protected static $_read = null;
    protected static $_write = null;

    function __construct()
    {
        parent::__construct();

        self::$_read = Mage::registry('resources')->getConnection('catalog_read');
        self::$_write = Mage::registry('resources')->getConnection('catalog_write');
    }

}
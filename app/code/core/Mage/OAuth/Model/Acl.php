<?php

class Mage_OAuth_Model_Acl extends Zend_Acl
{
    private static $_instance;

    /**
     *
     * @param $reload
     * @static
     * @return Mage_OAuth_Model_Acl
     */
    public static function getInstance($reload = false)
    {
        if (!self::$_instance || $reload) {
            /*$filename = dirname(__FILE__).'/Acl/data';
            $string = file_get_contents($filename);
            self::$_instance = unserialize($string);*/

            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('admin'));

        $this->addResource(new Zend_Acl_Resource('product'));
        $this->allow('guest', 'product', array('create', 'retrieve', 'update', 'delete'));
        $this->allow('admin', 'product', array('create', 'retrieve', 'update', 'delete'));

        $this->addResource(new Zend_Acl_Resource('products'));
        $this->allow('guest', 'products', array('create', 'retrieve', 'update', 'delete'));
        $this->allow('admin', 'products', array('create', 'retrieve', 'update', 'delete'));

        $this->addResource(new Zend_Acl_Resource('customer'));
        $this->allow('guest', 'customer', array('retrieve'));
        $this->allow('admin', 'customer', array('create', 'retrieve', 'update', 'delete'));

        $this->addResource(new Zend_Acl_Resource('customers'));
        $this->allow('guest', 'customers', array('retrieve'));
        $this->allow('admin', 'customers', array('create', 'retrieve', 'update', 'delete'));
    }

    private function __clone()
    {

    }

    public function save()
    {
        $filename = dirname(__FILE__).'/Acl/data';

        $string = serialize($this);
        file_put_contents($filename, $string);
    }
}

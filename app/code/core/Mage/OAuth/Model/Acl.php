<?php

class Mage_OAuth_Model_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('admin'));

        $this->addResource(new Zend_Acl_Resource('product'));
        $this->allow('guest', 'product', array('retrieve'));
        $this->allow('admin', 'product', array('create', 'retrieve', 'update', 'delete'));

        $this->addResource(new Zend_Acl_Resource('products'));
        $this->allow('guest', 'products', array('retrieve'));
        $this->allow('admin', 'products', array('create', 'retrieve', 'update', 'delete'));

        $this->addResource(new Zend_Acl_Resource('customer'));
        $this->allow('guest', 'customer', array('retrieve'));
        $this->allow('admin', 'customer', array('create', 'retrieve', 'update', 'delete'));

        $this->addResource(new Zend_Acl_Resource('customers'));
        $this->allow('guest', 'customers', array('retrieve'));
        $this->allow('admin', 'customers', array('create', 'retrieve', 'update', 'delete'));

    }

}

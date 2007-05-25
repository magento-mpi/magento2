<?php

/**
 * Core session model
 * 
 * @todo extend from Mage_Core_Model_Session_Abstract
 *
 */
class Mage_Core_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct($data=array())
    {
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('session')));
        Zend_Session::setSaveHandler(Mage::getModel('core_resource', 'session'));
        Zend_Session::start();

        $this->init('core');
    }
}
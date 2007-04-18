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
        $this->init('core');
    }

    public function getWebsite()
    {
        if (!$this->_session->website) {
            $this->setWebsite();
        }
        return $this->_session->website;
    }
    
    public function setWebsite($code='')
    {
        if (empty($code)) {
            $code = (string)Mage::getConfig()->getWebsiteConfig()->code;
        }
        $this->_session->website = Mage::getModel('core', 'website')->setWebsiteCode($code);
        $this->_session->website->load($code);
        return $this;
    }
}
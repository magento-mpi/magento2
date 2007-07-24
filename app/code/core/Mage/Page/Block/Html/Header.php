<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Page_Block_Html_Header extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('page/html/header.phtml');
    }

    public function setLogo($logo_src, $logo_alt)
    {
        $this->_logo_src = $logo_src;
        $this->_logo_alt = $logo_alt;
        return $this;
    }
    
    public function getLogoSrc()
    {
        if (!$this->_logo_src) {
            $this->_logo_src = $this->getDesignConfig('page/header/logo_src');
        }
        return $this->getSkinUrl($this->_logo_src);
    }

    public function getLogoAlt()
    {
        if (!$this->_logo_alt) {
            $this->_logo_alt = $this->getDesignConfig('page/header/logo_alt');
        }
        return $this->_logo_alt;
    }
    
    public function setWelcome($welcome)
    {
        $this->_welcome = $welcome;
        return $this;
    }
    
    public function getWelcome()
    {
        if (!$this->_welcome && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_welcome = __('Welcome, ' . Mage::getSingleton('customer/session')->getCustomer()->getName() . '!');
        }
        elseif (!$this->_welcome) {
            $this->_welcome = $this->getDesignConfig('page/header/welcome');
        }
            
        return $this->_welcome;
    }

}

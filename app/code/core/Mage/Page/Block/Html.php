<?php
/**
 * Html page block
 *
 * @package     Mage
 * @subpackage  Page
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Page_Block_Html extends Mage_Core_Block_Template
{
    protected $_urls = array();
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('page/3columns.phtml');
        $this->_urls = array(
            'base'      => Mage::getBaseUrl(),
            'baseSecure'=> Mage::getBaseUrl(array('_secure'=>true)),
            'skin'      => Mage::getBaseUrl(array('_type'=>'skin')),
            'js'        => Mage::getBaseUrl(array('_type'=>'js')),
            'current'   => $this->getRequest()->getRequestUri()
        );
    }
    
    public function getBaseUrl()
    {
        return $this->_urls['base'];
    }

    public function getBaseSecureUrl()
    {
        return $this->_urls['baseSecure'];
    }
/*
    public function getSkinUrl()
    {
        return $this->_urls['skin'];
    }
*/
    public function getJsUrl()
    {
        return $this->_urls['js'];
    }

    public function getCurrentUrl()
    {
        return $this->_urls['current'];
    }
}

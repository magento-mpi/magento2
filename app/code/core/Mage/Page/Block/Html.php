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
        $this->setTemplate('page/2columns/left.phtml');
        $this->_urls = array(
            'base'      => Mage::getBaseUrl(),
            'baseSecure'=> Mage::getBaseUrl(array('_secure'=>true)),
            'skin'      => Mage::getBaseUrl(array('_type'=>'skin')),
            'js'        => Mage::getBaseUrl(array('_type'=>'js')),
            'current'   => $this->getRequest()->getRequestUri()
        );
    }
    
    protected function _initChildren()
    {
        $this->append($this->getLayout()->createBlock('core/text_list', 'head'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'top.links'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'top.menu'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'top.forms'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'left'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'content'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'right'));
        $this->append($this->getLayout()->createBlock('core/text_list', 'botom.links'));
        
        $this->append($this->getLayout()->createBlock('core/messages', 'messages'));
    }
    
    public function getBaseUrl()
    {
        return $this->_urls['base'];
    }

    public function getBaseSecureUrl()
    {
        return $this->_urls['baseSecure'];
    }

    public function getSkinUrl()
    {
        return $this->_urls['skin'];
    }

    public function getJsUrl()
    {
        return $this->_urls['js'];
    }

    public function getCurrentUrl()
    {
        return $this->_urls['current'];
    }
}

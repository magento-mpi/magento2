<?php
/**
 * Adminhtml header block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Page_Header extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('page/header.phtml');
    }
    
    public function toHtml()
    {
        $this->assign('homeLink', Mage::getUrl('*'));
        $this->assign('user', Mage::getSingleton('admin/session')->getUser());
        $this->assign('logoutLink', Mage::getUrl('*/index/logout'));
        return parent::toHtml();
    }
}

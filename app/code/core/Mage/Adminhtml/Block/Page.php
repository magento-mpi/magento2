<?php
/**
 * Adminhtml page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Page extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/page.phtml');
    }
    
    public function _beforeToHtml()
    {
        $this->assign('baseUrl', Mage::getBaseUrl());
        $this->assign('baseSkinUrl', Mage::getBaseUrl().'skins/admin');
        return parent::_beforeToHtml();
    }
}

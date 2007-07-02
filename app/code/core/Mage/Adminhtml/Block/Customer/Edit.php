<?php
/**
 * Customer edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/customer/edit.phtml');
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml', array('controller'=>'customer', 'action'=>'save'));
    }

    protected function _beforeToHtml()
    {
        $this->assign('header', __('edit customer'));
        return $this;
    }
}

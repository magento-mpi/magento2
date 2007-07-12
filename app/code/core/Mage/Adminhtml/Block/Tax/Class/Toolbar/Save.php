<?php
/**
 * Admin tax class save toolbar
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Toolbar_Save extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('createUrl', Mage::getUrl('adminhtml/tax_class_customer/save'));
        $this->setTemplate('adminhtml/tax/toolbar/class/save.phtml');
    }
}
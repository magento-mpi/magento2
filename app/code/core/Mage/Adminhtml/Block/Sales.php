<?php
/**
 * Adminhtml sales page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Sales extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/sales/index.phtml');
    }

    public function _beforeToHtml()
    {
        $this->assign('createUrl', Mage::getUrl('adminhtml/sales/new'));
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/sales_grid', 'sales.grid'));
        return $this;
    }
}

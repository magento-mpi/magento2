<?php
/**
 * Adminhtml sales order create select store block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Store_Select extends Mage_Core_Block_Template
{

    protected $_websiteCollection = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sc_store_select');
        $this->setTemplate('sales/order/create/store/select.phtml');
    }

    public function getWebsiteCollection()
    {
        if (is_null($this->_websiteCollection)) {
            $this->_websiteCollection = Mage::getModel('core/website')->getResourceCollection()->load();
        }
        return $this->_websiteCollection;
    }

}

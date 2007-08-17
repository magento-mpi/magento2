<?php
/**
 * Adminhtml sales order create sidebar block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    protected $_items = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/create/sidebar/items.phtml');
    }

    public function hasItems()
    {
        if (is_null($this->_items)) {
            $this->_prepareItems();
        }
        if (! empty($this->_items) && (count($this->_items))) {
            return true;
        }
        return false;
    }

    public function getItems()
    {
        return $this->_items;
    }

    public function setItems($items)
    {
        $this->_items = $items;
        return $this;
    }

    protected function _prepareItems()
    {
        return $this;
    }

    public function toHtml()
    {
        if ($this->hasItems()) {
            return parent::toHtml();
        }
        return '';
    }

}

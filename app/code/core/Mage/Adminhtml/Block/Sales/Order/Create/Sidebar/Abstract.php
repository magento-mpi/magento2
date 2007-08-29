<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create sidebar block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
        if (! $this->getStoreId()) {
            return false;
        }
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

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
 * Adminhtml sales order create sidebar compared block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Compared extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_sidebar_compared');
    }
    
    protected function _prepareItems()
    {
        if (!is_null($this->_itemCollection)) {
            return $this;
        }
        if (($customerId = $this->getCustomerId()) && $this->getStoreId()) {
            $this->_itemCollection = Mage::getModel('catalog/product_compare_list')->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->getStoreId())
                ->setCustomerId($customerId)
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('status')
                ->load();
        }
        return $this;
    }

    public function getHeaderText()
    {
        return __('Recently Compared');
    }

}

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
 * Create order form header
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Header extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function toHtml()
    {
        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '<h3>';
        if ($customerId && $storeId) {
            $out.= __('Create New Order for %s in %s', $this->getCustomer()->getName(), $this->getStore()->getName());
        }
        elseif (!is_null($customerId) && $storeId){
            $out.= __('Create New Order for New Customer in %s', $this->getStore()->getName());
        }
        elseif ($customerId) {
        	$out.= __('Create New Order for %s', $this->getCustomer()->getName());
        }
        elseif (!is_null($customerId)){
            $out.= __('Create New Order for New Customer');
        }
        else {
            $out.= __('Create New Order');
        }
        $out .= '</h3>';
        return $out;
    }
}

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
 * Order create data
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Data extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('sales/order/create/data.phtml');
    }
    
    protected function _prepareLayout()
    {
        $childNames = array(
            'shipping_address',
            'billing_address',
            'shipping_method',
            'billing_method',
            'coupons',
            //'newsletter',
            'search',
            'items',
            'totals'
        );

        foreach ($childNames as  $name) {
            $this->setChild($name, $this->getLayout()->createBlock('adminhtml/sales_order_create_' . $name));
        }
        return parent::_prepareLayout();
    }    
}

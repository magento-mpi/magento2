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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order details block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Block_Order_Details extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/details.phtml');
        $this->setOrder(Mage::getModel('Mage_Sales_Model_Order')->load($this->getRequest()->getParam('order_id')));
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('Mage_Sales_Helper_Data')->__('Order Details'));
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }


    public function getInvoices()
    {
        $invoices = Mage::getModel('Mage_Sales_Model_Entity_Order_Invoice_Collection')->setOrderFilter($this->getOrder()->getId())->load();
        return $invoices;
    }

    public function getPrintUrl()
    {
        return Mage::getUrl('*/*/print');
    }

}

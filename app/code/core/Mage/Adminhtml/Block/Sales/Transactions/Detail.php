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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml transaction detail
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Transactions_Detail extends Enterprise_Enterprise_Block_Adminhtml_Widget_Container
{
    /**
     * Transaction model
     *
     * @var Mage_Sales_Model_Order_Payment_Transaction
     */
    protected $_txn;

    /**
     * Add control buttons
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_addButton('back', array(
            'label'   => Mage::helper('sales')->__('Back'),
            'onclick' => "setLocation('" . Mage::getSingleton('adminhtml/url')->getUrl('*/*/'). "')",
            'class'   => 'back'
        ));

        $this->_txn = Mage::registry('current_transaction');
        if (Mage::getSingleton('admin/session')->isAllowed('sales/transactions/fetch')
            && $this->_txn->getOrderPaymentObject()->getMethodInstance()->canFetchTransactionInfo()) {
            $this->_addButton('fetch', array(
                'label'   => Mage::helper('sales')->__('Fetch'),
                'onclick' => "setLocation('" . Mage::getSingleton('adminhtml/url')->getUrl('*/*/fetch' , array('_current' => true)). "')",
                'class'   => 'button'
            ));
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__("Transaction # %s | %s",
            $this->_txn->getTxnId(),
            $this->formatDate($this->_txn->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
        );
    }

    protected function _toHtml()
    {
        $this->addData($this->_txn->getData());

        $this->setParentTxnIdUrl(
            $this->getUrl('*/sales_transactions/view', array('txn_id' => $this->getParentId()))
        );

        $this->setOrderIncrementId($this->_txn->getOrder()->getIncrementId());

        $this->setOrderIdUrl(
            $this->getUrl('*/sales_order/view', array('order_id' => $this->getOrderId()))
        );

        $createdAt = (strtotime($this->getCreatedAt()))
            ? $this->formatDate($this->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
            : $this->__('N/A');
        $this->setCreatedAt($createdAt);

        return parent::_toHtml();
    }
}
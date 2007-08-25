<?php
/**
 * Adminhtml order edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Edit_Form extends Mage_Core_Block_Template
{
    /**
     * Enter description here...
     *
     * @var array
     */
    protected $_statuses;

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_form');
        $this->setTitle(__('Order Information'));
        $this->setTemplate('sales/order/edit/form.phtml');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Edit_Form
     */
    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild( 'items', $this->getLayout()->createBlock( 'adminhtml/sales_order_edit_items', 'items' ));
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('items');
    }

    /**
     * Enter description here...
     *
     * @param string $format
     * @return string
     */
    public function getOrderDateFormatted($format='short')
    {
        $dateFormatted = strftime(Mage::getStoreConfig('general/local/date_format_' . $format), strtotime($this->getOrder()->getCreatedAt()));
        return $dateFormatted;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return Mage::getModel('sales/order_status')->load($this->getOrder()->getOrderStatusId())->getFrontendLabel();
    }

    public function getSaveUrl()
    {
        return $this->getParentBlock()->getSaveUrl();
    }

    public function getStatuses()
    {
        if (is_null($this->_statuses)) {
            $this->_statuses = Mage::getResourceModel('sales/order_status_collection')->load()->toOptionHash();
        }
        return $this->_statuses;
    }

    public function formatDate($date, $format='medium')
    {
        $dateFormatted = strftime(Mage::getStoreConfig('general/local/datetime_format_' . $format), strtotime($date));
        return $dateFormatted;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        $methodName = $this->getOrder()->getPayment()->getMethod();
        $html = '';
        $methodConfig = new Varien_Object(Mage::getStoreConfig('payment/' . $this->getOrder()->getPayment()->getMethod(), $this->getOrder()->getStoreId()));
        if ($methodConfig) {
            $className = $methodConfig->getModel();
            $method = Mage::getModel($className);
            if ($method) {
                $method->setPayment($this->getOrder()->getPayment());
            	$methodBlock = $method->createInfoBlock('payment.method.'.$methodName);
            	if (!empty($methodBlock)) {
            	    $html = $methodBlock->toHtml();
    	        }
            }
        }
        return $html;
    }

}

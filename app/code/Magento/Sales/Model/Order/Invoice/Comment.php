<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_Sales_Model_Resource_Order_Invoice_Comment _getResource()
 * @method Magento_Sales_Model_Resource_Order_Invoice_Comment getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Invoice_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Magento_Sales_Model_Order_Invoice_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Magento_Sales_Model_Order_Invoice_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Magento_Sales_Model_Order_Invoice_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Invoice_Comment setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Invoice_Comment extends Magento_Sales_Model_Abstract
{
    /**
     * Invoice instance
     *
     * @var Magento_Sales_Model_Order_Invoice
     */
    protected $_invoice;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Invoice_Comment');
    }

    /**
     * Declare invoice instance
     *
     * @param   Magento_Sales_Model_Order_Invoice $invoice
     * @return  Magento_Sales_Model_Order_Invoice_Comment
     */
    public function setInvoice(Magento_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Get store object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getInvoice()) {
            return $this->getInvoice()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Invoice_Comment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getInvoice()) {
            $this->setParentId($this->getInvoice()->getId());
        }

        return $this;
    }
}

<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Invoice_Comment getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Invoice_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Mage_Sales_Model_Order_Invoice_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Mage_Sales_Model_Order_Invoice_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Mage_Sales_Model_Order_Invoice_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Order_Invoice_Comment setCreatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Invoice_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Invoice instance
     *
     * @var Mage_Sales_Model_Order_Invoice
     */
    protected $_invoice;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Resource_Order_Invoice_Comment');
    }

    /**
     * Declare invoice instance
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  Mage_Sales_Model_Order_Invoice_Comment
     */
    public function setInvoice(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
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
     * @return Mage_Sales_Model_Order_Invoice_Comment
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

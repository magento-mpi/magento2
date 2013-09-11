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
 * @method \Magento\Sales\Model\Resource\Order\Invoice\Comment _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Invoice\Comment getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Invoice\Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method \Magento\Sales\Model\Order\Invoice\Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method \Magento\Sales\Model\Order\Invoice\Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method \Magento\Sales\Model\Order\Invoice\Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Invoice\Comment setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Invoice;

class Comment extends \Magento\Sales\Model\AbstractModel
{
    /**
     * Invoice instance
     *
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $_invoice;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Resource\Order\Invoice\Comment');
    }

    /**
     * Declare invoice instance
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  \Magento\Sales\Model\Order\Invoice\Comment
     */
    public function setInvoice(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Get store object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if ($this->getInvoice()) {
            return $this->getInvoice()->getStore();
        }
        return \Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return \Magento\Sales\Model\Order\Invoice\Comment
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

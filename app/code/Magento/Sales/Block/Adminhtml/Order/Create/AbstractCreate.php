<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

/**
 * Adminhtml sales order create abstract block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractCreate extends \Magento\Backend\Block\Widget
{
    /**
     * Session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * Order create
     *
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $_orderCreate;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        $this->_orderCreate = $orderCreate;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve create order model object
     *
     * @return \Magento\Sales\Model\AdminOrder\Create
     */
    public function getCreateOrderModel()
    {
        return $this->_orderCreate;
    }

    /**
     * Retrieve quote session object
     *
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_sessionQuote;
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getSession()->getCustomerId();
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    /**
     * Retrieve store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getSession()->getStoreId();
    }

    /**
     * Retrieve formated price
     *
     * @param decimal $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }

    /**
     * Convert price
     *
     * @param float $value
     * @param bool $format
     * @return float
     */
    public function convertPrice($value, $format=true)
    {
        return $this->getStore()->convertPrice($value, $format);
    }
}

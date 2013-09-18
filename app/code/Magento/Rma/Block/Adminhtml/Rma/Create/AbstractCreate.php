<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create form header
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Create;

abstract class AbstractCreate extends \Magento\Adminhtml\Block\Widget
{
     /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve create order model object
     *
     * @return \Magento\Rma\Model\Rma\Create
     */
    public function getCreateRmaModel()
    {
        return $this->_coreRegistry->registry('rma_create_model');
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return (int)$this->getCreateRmaModel()->getCustomerId();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->getCreateRmaModel()->getStoreId();
    }

    /**
     * Retrieve customer object
     *
     * @return int
     */
    public function getCustomer()
    {
        return $this->getCreateRmaModel()->getCustomer();
    }

    /**
     * Retrieve customer name
     *
     * @return int
     */
    public function getCustomerName()
    {
        return $this->escapeHtml($this->getCustomer()->getName());
    }

    /**
     * Retrieve order identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return (int)$this->getCreateRmaModel()->getOrderId();
    }

    /**
     * Set Customer Id
     *
     * @param int $id
     */
    public function setCustomerId($id)
    {
        $this->getCreateRmaModel()->setCustomerId($id);
    }

    /**
     * Set Order Id
     *
     * @param int $id
     */
    public function setOrderId($id)
    {
        return $this->getCreateRmaModel()->setOrderId($id);
    }

}

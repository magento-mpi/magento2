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

abstract class Magento_Rma_Block_Adminhtml_Rma_Create_Abstract extends Magento_Adminhtml_Block_Widget
{
     /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve create order model object
     *
     * @return Magento_Rma_Model_Rma_Create
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

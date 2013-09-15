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
 * Request Details Block at RMA page
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class Returnaddress
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
{
    /**
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $registry, $data);
    }

    /**
     * Constructor
     */
    public function _construct()
    {
        $order = $this->_coreRegistry->registry('current_order');
        $rma = $this->_coreRegistry->registry('current_rma');
        if ($order && $order->getId()) {
            $this->setStoreId($order->getStoreId());
        } elseif ($rma && $rma->getId()) {
            $this->setStoreId($rma->getStoreId());
        }
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getReturnAddress()
    {
        return $this->_rmaData->getReturnAddress('html', array(), $this->getStoreId());
    }

}

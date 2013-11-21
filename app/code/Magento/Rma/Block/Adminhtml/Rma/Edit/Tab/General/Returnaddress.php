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
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Registry $registry,
        \Magento\Rma\Helper\Data $rmaData,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($context, $coreData, $registry, $data);
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

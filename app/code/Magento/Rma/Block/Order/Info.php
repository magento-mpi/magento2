<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Order;

class Info extends \Magento\Core\Block\Template
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rmaData = $rmaData;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        if ($this->_rmaData->isEnabled()) {
            $returns = \Mage::getResourceModel('Magento\Rma\Model\Resource\Rma\Grid\Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getId())
                ->addFieldToFilter('order_id', $this->_coreRegistry->registry('current_order')->getId())
                ->count()
            ;

            if (!empty($returns)) {
                \Mage::app()->getLayout()
                    ->getBlock('sales.order.info')
                    ->addLink('returns', 'rma/return/returns', 'Returns');
            }
        }
    }
}

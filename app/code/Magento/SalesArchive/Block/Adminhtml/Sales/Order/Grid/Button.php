<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Add sales archiving to order's grid view massaction
 *
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid;

class Button extends \Magento\Adminhtml\Block\Sales\Order\AbstractOrder
{
    /**
     * @var \Magento\SalesArchive\Model\Resource\Order\Collection
     */
    protected $_orderCollection;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection,
        array $data = array()
    ) {
        $this->_orderCollection = $orderCollection;
        parent::__construct($coreData, $context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        $ordersCount = $this->_orderCollection->getSize();
        $parent = $this->getLayout()->getBlock('sales_order.grid.container');
        if ($parent && $ordersCount) {
            $url = $this->getUrl('*/sales_archive/orders');
            $parent->addButton('go_to_archive',  array(
                'label'     => __('Go to Archive (%1 orders)', $ordersCount),
                'onclick'   => 'setLocation(\'' . $url . '\')',
                'class'     => 'go'
            ));
        }
        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}

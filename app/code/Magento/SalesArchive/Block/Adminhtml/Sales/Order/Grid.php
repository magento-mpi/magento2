<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order;

/**
 *  Add sales archiving to order's grid view massaction
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order
{
    /**
     * @var \Magento\SalesArchive\Model\Resource\Order\Collection
     */
    protected $orderCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\SalesArchive\Model\Resource\Order\Collection $orderCollection,
        array $data = array()
    ) {
        $this->orderCollection = $orderCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $ordersCount = $this->orderCollection->getSize();
        if ($ordersCount) {
            $url = $this->getUrl('sales/archive/orders');
            $this->addButton(
                'go_to_archive',
                array(
                    'label' => __('Go to Archive (%1 orders)', $ordersCount),
                    'onclick' => 'setLocation(\'' . $url . '\')',
                    'class' => 'go'
                )
            );
        }
    }
}

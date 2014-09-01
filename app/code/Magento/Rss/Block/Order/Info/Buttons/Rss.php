<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rss\Block\Order\Info\Buttons;

/**
 * Block with rss feed link in Order view page
 */
class Rss extends \Magento\Framework\View\Element\Template
{
    /**
     * Template of the block
     *
     * @var string
     */
    protected $_template = 'order/info/buttons/rss.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Rss\Helper\Order
     */
    protected $orderHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Rss\Helper\Order $orderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Rss\Helper\Order $orderHelper,
        array $data = array()
    ) {
        $this->registry = $registry;
        $this->orderHelper = $orderHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * Getting order helper
     *
     * @return \Magento\Rss\Helper\Order
     */
    public function getOrderHelper()
    {
        return $this->orderHelper;
    }
}

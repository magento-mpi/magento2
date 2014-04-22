<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Sales\Model\Order;

/**
 * Order view messages
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Messages extends \Magento\Framework\View\Element\Messages
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        \Magento\Message\ManagerInterface $messageManager,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $messageFactory, $collectionFactory, $messageManager, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return Order
     */
    protected function _getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /**
         * Check Item products existing
         */
        $productIds = array();
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        return parent::_prepareLayout();
    }
}

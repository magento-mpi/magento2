<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order view messages
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\View;

class Messages extends \Magento\Adminhtml\Block\Messages
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Message\Factory $messageFactory
     * @param \Magento\Message\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Message\Factory $messageFactory,
        \Magento\Message\CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($coreData, $context, $messageFactory, $collectionFactory, $data);
    }

    protected function _getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

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

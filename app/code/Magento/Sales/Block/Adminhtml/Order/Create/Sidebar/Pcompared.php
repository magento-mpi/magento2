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
 * Adminhtml sales order create sidebar recently compared block
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Sales\Block\Adminhtml\Order\Create\Sidebar;

class Pcompared extends \Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\AbstractSidebar
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Reports\Model\Resource\Event
     */
    protected $_event;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Reports\Model\Resource\Event $event
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Reports\Model\Resource\Event $event,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        $this->_event = $event;
        $this->_productFactory = $productFactory;
        parent::__construct($context, $sessionQuote, $orderCreate, $salesConfig, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_pcompared');
        $this->setDataId('pcompared');
    }

    public function getHeaderText()
    {
        return __('Recently Compared Products');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $productCollection = $this->getData('item_collection');
        if (is_null($productCollection)) {
            // get products to skip
            $skipProducts = array();
            if ($collection = $this->getCreateOrderModel()->getCustomerCompareList()) {
                $collection = $collection->getItemCollection()
                    ->useProductItem(true)
                    ->setStoreId($this->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                    ->load();
                foreach ($collection as $_item) {
                    $skipProducts[] = $_item->getProductId();
                }
            }

            // prepare products collection and apply visitors log to it
            $productCollection = $this->_productFactory->create()->getCollection()
                ->setStoreId($this->getQuote()->getStoreId())
                ->addStoreFilter($this->getQuote()->getStoreId())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image');
            $this->_event->applyLogToCollection(
                $productCollection, \Magento\Reports\Model\Event::EVENT_PRODUCT_COMPARE, $this->getCustomerId(), 0, $skipProducts
            );

            $productCollection->load();
            $this->setData('item_collection', $productCollection);
        }
        return $productCollection;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return bool
     */
    public function canRemoveItems()
    {
        return false;
    }

    /**
     * Get product Id
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier of block item
     *
     * @param   mixed $item
     * @return  int
     */
    public function getProductId($item) {
        return $item->getId();
    }
}

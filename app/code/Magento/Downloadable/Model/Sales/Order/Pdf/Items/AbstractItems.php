<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order Downloadable Pdf Items renderer
 */
namespace Magento\Downloadable\Model\Sales\Order\Pdf\Items;

abstract class AbstractItems extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{
    /**
     * Downloadable links purchased model
     *
     * @var \Magento\Downloadable\Model\Link\Purchased
     */
    protected $_purchasedLinks = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Downloadable\Model\Link\PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Dir $coreDir
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory
     * @param \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Dir $coreDir,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory,
        \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($taxData, $context, $registry, $coreDir, $resource, $resourceCollection, $data);
    }

    /**
     * Return Purchased link for order item
     *
     * @return \Magento\Downloadable\Model\Link\Purchased
     */
    public function getLinks()
    {
        $this->_purchasedLinks = $this->_purchasedFactory->create()->load($this->getOrder()->getId(), 'order_id');
        $purchasedItems = $this->_itemsFactory->create()->addFieldToFilter(
            'order_item_id',
            $this->getItem()->getOrderItem()->getId()
        );
        $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    /**
     * Return Links Section Title for order item
     *
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return $this->_coreStoreConfig->getConfig(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE);
    }
}

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
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($taxData, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Return Purchased link for order item
     *
     * @return \Magento\Downloadable\Model\Link\Purchased
     */
    public function getLinks()
    {
        $this->_purchasedLinks = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased')
            ->load($this->getOrder()->getId(), 'order_id');
        $purchasedItems = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased\Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getOrderItem()->getId());
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

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
 * Downloadable Sales Order Email items renderer
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Sales\Order\Email\Items\Order;

class Downloadable extends \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder
{
    /**
     * @var \Magento\Downloadable\Model\Link\Purchased
     */
    protected $_purchased;

    /**
     * @var \Magento\Downloadable\Model\Link\PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory
     */
    protected $_itemsFactory;

    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory,
        \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory,
        array $data = array()
    ) {
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
        $this->_purchased = $this->_purchasedFactory->create()->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedLinks = $this->_itemsFactory->create()->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedLinks);

        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return $this->_storeConfig->getConfig(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE);
    }

    public function getPurchasedLinkUrl($item)
    {
        return $this->getUrl('downloadable/download/link', array(
            'id'        => $item->getLinkHash(),
            '_store'    => $this->getOrder()->getStore(),
            '_secure'   => true,
            '_nosid'    => true
        ));
    }
}

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
 * Downlaodable Sales Order Email items renderer
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Sales\Order\Email\Items;

class Downloadable extends \Magento\Sales\Block\Order\Email\Items\DefaultItems
{
    protected $_purchased = null;

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
        $this->_purchased = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased')
            ->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedLinks = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased\Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getOrderItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedLinks);

        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return \Mage::getStoreConfig(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE);
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

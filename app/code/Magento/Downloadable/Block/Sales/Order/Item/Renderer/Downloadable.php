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
 * Downloadable order item render block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Sales\Order\Item\Renderer;

class Downloadable extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer
{
    protected $_purchasedLinks = null;

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getLinks()
    {
            $this->_purchasedLinks = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased')
                ->load($this->getOrderItem()->getOrder()->getId(), 'order_id');
            $purchasedItems = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased\Item')->getCollection()
                ->addFieldToFilter('order_item_id', $this->getOrderItem()->getId());
            $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return $this->_storeConfig->getConfig(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE);
    }

}

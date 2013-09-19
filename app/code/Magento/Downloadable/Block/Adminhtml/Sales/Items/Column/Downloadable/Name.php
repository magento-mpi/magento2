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
 * Sales Order downloadable items name column renderer
 *
 * @category   Magento
 * @package    Magento_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Adminhtml\Sales\Items\Column\Downloadable;

class Name extends \Magento\Adminhtml\Block\Sales\Items\Column\Name
{
    protected $_purchased = null;
    public function getLinks()
    {
        $this->_purchased = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased')
            ->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedItem = \Mage::getModel('Magento\Downloadable\Model\Link\Purchased\Item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getItem()->getId());
        $this->_purchased->setPurchasedItems($purchasedItem);
        return $this->_purchased;
    }

    public function getLinksTitle()
    {
        if ($this->_purchased && $this->_purchased->getLinkSectionTitle()) {
            return $this->_purchased->getLinkSectionTitle();
        }
        return $this->_storeConfig->getConfig(\Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE);
    }
}
?>

<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Block\Adminhtml\Sales\Items\Column\Downloadable;

/**
 * Sales Order downloadable items name column renderer
 */
class Name extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    protected $_purchased = null;

    /**
     * @var \Magento\Downloadable\Model\Link\PurchasedFactory
     */
    protected $_purchasedFactory;

    /**
     * @var \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory
     * @param \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory
     * @param array $data
     * @internal param \Magento\Core\Helper\String $coreString
     */
    public function __construct(
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory,
        \Magento\Downloadable\Model\Resource\Link\Purchased\Item\CollectionFactory $itemsFactory,
        array $data = array()
    ) {
        $this->_purchasedFactory = $purchasedFactory;
        $this->_itemsFactory = $itemsFactory;
        parent::__construct($optionFactory, $coreData, $context, $data);
    }

    public function getLinks()
    {
        $this->_purchased = $this->_purchasedFactory->create()->load($this->getItem()->getOrder()->getId(), 'order_id');
        $purchasedItem = $this->_itemsFactory->create()->addFieldToFilter('order_item_id', $this->getItem()->getId());
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

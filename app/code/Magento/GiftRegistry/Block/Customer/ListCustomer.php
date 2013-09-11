<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer giftregistry list block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
namespace Magento\GiftRegistry\Block\Customer;

class ListCustomer
    extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Instantiate pagination
     *
     * @return \Magento\GiftRegistry\Block\Customer\ListCustomer
     */
    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('\Magento\Page\Block\Html\Pager', 'giftregistry.list.pager')
            ->setCollection($this->getEntityCollection())->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
        return parent::_prepareLayout();
    }

    /**
     * Return list of gift registries
     *
     * @return \Magento\GiftRegistry\Model\Resource\GiftRegistry\Collection
     */
    public function getEntityCollection()
    {
        if (!$this->hasEntityCollection()) {
            $this->setData('entity_collection', \Mage::getModel('\Magento\GiftRegistry\Model\Entity')->getCollection()
                ->filterByCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
            );
        }
        return $this->_getData('entity_collection');
    }

    /**
     * Check exist listed gift registry types on the current store
     *
     * @return bool
     */
    public function canAddNewEntity()
    {
        $collection = \Mage::getModel('\Magento\GiftRegistry\Model\Type')->getCollection()
            ->addStoreData(\Mage::app()->getStore()->getId())
            ->applyListedFilter();

        return (bool)$collection->getSize();
    }

    /**
     * Return add button form url
     *
     * @return string
     */
    public function getAddUrl()
    {
        return $this->getUrl('giftregistry/index/addselect');
    }

    /**
     * Return view entity items url
     *
     * @return string
     */
    public function getItemsUrl($item)
    {
        return $this->getUrl('giftregistry/index/items', array('id' => $item->getEntityId()));
    }

    /**
     * Return share entity url
     *
     * @return string
     */
    public function getShareUrl($item)
    {
        return $this->getUrl('giftregistry/index/share', array('id' => $item->getEntityId()));
    }

    /**
     * Return edit entity url
     *
     * @return string
     */
    public function getEditUrl($item)
    {
        return  $this->getUrl('giftregistry/index/edit', array('entity_id' => $item->getEntityId()));
    }

    /**
     * Return delete entity url
     *
     * @return string
     */
    public function getDeleteUrl($item)
    {
        return $this->getUrl('giftregistry/index/delete', array('id' => $item->getEntityId()));
    }

    /**
     * Retrieve item title
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getEscapedTitle($item)
    {
        return $this->escapeHtml($item->getData('title'));
    }

    /**
     * Retrieve item formated date
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate($item->getCreatedAt(), \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Retrieve escaped item message
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getEscapedMessage($item)
    {
        return $this->escapeHtml($item->getData('message'));
    }

    /**
     * Retrieve item message
     *
     * @param \Magento\GiftRegistry\Model\Entity $item
     * @return string
     */
    public function getIsActive($item)
    {
        return $item->getData('is_active');
    }
}

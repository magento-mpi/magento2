<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer giftregistry list block
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Customer_List
    extends Magento_Customer_Block_Account_Dashboard
{
    /**
     * Instantiate pagination
     *
     * @return Enterprise_GiftRegistry_Block_Customer_List
     */
    protected function _prepareLayout()
    {
        $pager = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'giftregistry.list.pager')
            ->setCollection($this->getEntityCollection())->setIsOutputRequired(false);
        $this->setChild('pager', $pager);
        return parent::_prepareLayout();
    }

    /**
     * Return list of gift registries
     *
     * @return Enterprise_GiftRegistry_Model_Resource_GiftRegistry_Collection
     */
    public function getEntityCollection()
    {
        if (!$this->hasEntityCollection()) {
            $this->setData('entity_collection', Mage::getModel('Enterprise_GiftRegistry_Model_Entity')->getCollection()
                ->filterByCustomerId(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
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
        $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Type')->getCollection()
            ->addStoreData(Mage::app()->getStore()->getId())
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
     * @param Enterprise_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getEscapedTitle($item)
    {
        return $this->escapeHtml($item->getData('title'));
    }

    /**
     * Retrieve item formated date
     *
     * @param Enterprise_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate($item->getCreatedAt(), Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Retrieve escaped item message
     *
     * @param Enterprise_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getEscapedMessage($item)
    {
        return $this->escapeHtml($item->getData('message'));
    }

    /**
     * Retrieve item message
     *
     * @param Enterprise_GiftRegistry_Model_Entity $item
     * @return string
     */
    public function getIsActive($item)
    {
        return $item->getData('is_active');
    }
}

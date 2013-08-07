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
 * Gift registry quick search block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
class Magento_GiftRegistry_Block_Search_Quick extends Magento_Core_Block_Template
{
    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  Mage::helper('Magento_GiftRegistry_Helper_Data')->isEnabled();
    }

    /**
     * Return available gift registry types collection
     *
     * @return Magento_GiftRegistry_Model_Resource_Type_Collection
     */
    public function getTypesCollection()
    {
        return Mage::getModel('Magento_GiftRegistry_Model_Type')->getCollection()
            ->addStoreData(Mage::app()->getStore()->getId())
            ->applyListedFilter()
            ->applySortOrder();
    }

    /**
     * Select element for choosing registry type
     *
     * @return array
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setData(array(
                'id'    => 'quick_search_type_id',
                'class' => 'select'
            ))
            ->setName('params[type_id]')
            ->setOptions($this->getTypesCollection()->toOptionArray(true));
        return $select->getHtml();
    }

    /**
     * Return quick search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('giftregistry/search/results');
    }
}

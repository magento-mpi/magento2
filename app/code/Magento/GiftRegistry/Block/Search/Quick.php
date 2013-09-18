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
namespace Magento\GiftRegistry\Block\Search;

class Quick extends \Magento\Core\Block\Template
{
    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  $this->_giftRegistryData->isEnabled();
    }

    /**
     * Return available gift registry types collection
     *
     * @return \Magento\GiftRegistry\Model\Resource\Type\Collection
     */
    public function getTypesCollection()
    {
        return \Mage::getModel('Magento\GiftRegistry\Model\Type')->getCollection()
            ->addStoreData(\Mage::app()->getStore()->getId())
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
        $select = $this->getLayout()->createBlock('Magento\Core\Block\Html\Select')
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

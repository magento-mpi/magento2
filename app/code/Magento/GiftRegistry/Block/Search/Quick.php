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
     * @var Magento_GiftRegistry_Model_TypeFactory
     */
    protected $typeFactory;

    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_GiftRegistry_Model_TypeFactory $typeFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_GiftRegistry_Model_TypeFactory $typeFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->typeFactory = $typeFactory;
        parent::__construct($coreData, $context, $data);

        $this->storeManager = $storeManager;
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
     * @return Magento_GiftRegistry_Model_Resource_Type_Collection
     */
    public function getTypesCollection()
    {
        return $this->typeFactory->create()->getCollection()
            ->addStoreData($this->storeManager->getStore()->getId())
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

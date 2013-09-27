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
class Magento_GiftRegistry_Block_Customer_Edit extends Magento_Directory_Block_Data
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $customerSession;

    /**
     * @var Magento_GiftRegistry_Model_TypeFactory
     */
    protected $typeFactory;

    /**
     * Template container
     *
     * @var array
     */
    protected $_inputTemplates = array();

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_GiftRegistry_Model_TypeFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_GiftRegistry_Model_TypeFactory $typeFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->typeFactory = $typeFactory;

        parent::__construct($configCacheType, $coreData, $context, $storeManager, $regionCollFactory, $countryCollFactory, $data);

        $this->storeManager = $storeManager;
    }

    /**
     * Return edit form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        if ($this->_coreRegistry->registry('magento_giftregistry_entity')->getId()) {
            return __('Edit Gift Registry');
        } else {
            return __('Create Gift Registry');
        }
    }

    /**
     * Getter for post data, stored in session
     *
     * @return array|null
     */
    public function getFormDataPost()
    {
        return $this->customerSession->getGiftRegistryEntityFormData(true);
    }

    /**
     * Get array of reordered custom registry attributes
     *
     * @return array
     */
    public function getGroupedRegistryAttributes()
    {
        $attributes = $this->getEntity()->getCustomAttributes();
        return empty($attributes['registry']) ? array() : $this->_groupAttributes($attributes['registry']);
    }

    /**
     * Get array of reordered custom registrant attributes
     *
     * @return array
     */
    public function getGroupedRegistrantAttributes()
    {
        $attributes = $this->getEntity()->getCustomAttributes();
        return empty($attributes['registrant']) ? array() : $this->_groupAttributes($attributes['registrant']);
    }

    /**
     * Fetches type list array
     *
     * @return array
     */
    public function getTypeList()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->typeFactory->create()
            ->getCollection()
            ->addStoreData($storeId)
            ->applyListedFilter()
            ->applySortOrder();
        $list = $collection->toOptionArray();
        return $list;
    }

    /**
     * Return "create giftregistry" form Add url
     *
     * @return string
     */
    public function getAddActionUrl()
    {
        return $this->getUrl('magento_giftregistry/index/edit');
    }

    /**
     * Return form back link url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

    /**
     * Return "create giftregistry" form AddPost url
     *
     * @return string
     */
    public function getAddPostActionUrl()
    {
        return $this->getUrl('magento_giftregistry/index/addPost');
    }

    /**
     * Return "create giftregistry" form url
     *
     * @return string
     */
    public function getAddGiftRegistryUrl()
    {
        return $this->getUrl('magento_giftregistry/index/addselect');
    }

    /**
     * Return "create giftregistry" form url
     *
     * @return string
     */
    public function getSaveActionUrl()
    {
        return $this->getUrl('magento_giftregistry/index/save');
    }

    /**
     * Setup template from template file as $_inputTemplates['type'] for specified type
     *
     * @param string $type
     * @param string $template
     * @return Magento_GiftRegistry_Block_Customer_Edit
     */
    public function addInputTypeTemplate($type, $template)
    {
        $params = array('_relative'=>true);
        $area = $this->getArea();
        if ($area) {
            $params['area'] = $area;
        }
        $templateName = $this->_viewFileSystem->getFilename($template, $params);

        $this->_inputTemplates[$type] = $templateName;
        return $this;
    }

    /**
     * Return presetted template by type
     * @param string $type
     * @return string
     */
    public function getInputTypeTemplate($type)
    {
        if (isset($this->_inputTemplates[$type])) {
            return $this->_inputTemplates[$type];
        }
        return false;
    }
}

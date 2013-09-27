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
 * Gift registry search form
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 */
class Magento_GiftRegistry_Block_Search_Form extends Magento_Core_Block_Template
{
    protected $_formData = null;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $customerSession;

    /**
     * @var Magento_GiftRegistry_Model_TypeFactory
     */
    protected $typeFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_GiftRegistry_Model_TypeFactory $typeFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_GiftRegistry_Model_TypeFactory $typeFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->customerSession = $customerSession;
        $this->typeFactory = $typeFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        return __('Gift Registry Search');
    }

    /**
     * Retrieve by key saved in session form data
     *
     * @param string $key
     * @return mixed
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = $this->customerSession->getRegistrySearchData();
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        }
        return $this->escapeHtml($this->_formData[$key]);
    }

    /**
     * Return available gift registry types collection
     *
     * @return Magento_GiftRegistry_Model_Resource_Type_Collection
     */
    public function getTypesCollection()
    {
        return $this->typeFactory->create()->getCollection()
            ->addStoreData($this->storeManager->getStore()->getId());
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
                'id'    => 'params-type-id',
                'class' => 'select'
            ))
            ->setName('params[type_id]')
            ->setOptions($this->getTypesCollection()->toOptionArray(true));
        return $select->getHtml();
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('giftregistry/search/results');
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getAdvancedUrl()
    {
        return $this->getUrl('giftregistry/search/advanced');
    }
}

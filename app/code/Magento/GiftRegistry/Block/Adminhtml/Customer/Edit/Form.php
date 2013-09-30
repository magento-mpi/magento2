<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Form
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Magento_GiftRegistry_Model_TypeFactory
     */
    protected $giftRegistryTypeFactory;

    protected $_template = 'customer/form.phtml';

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
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_GiftRegistry_Model_TypeFactory $giftRegistryTypeFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_GiftRegistry_Model_TypeFactory $giftRegistryTypeFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->customerFactory = $customerFactory;
        $this->giftRegistryTypeFactory = $giftRegistryTypeFactory;
        parent::__construct($coreData, $context, $data);

        $this->storeManager = $storeManager;
    }

    /**
     * Prepare layout
     *
     * @return Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Form
     */
    protected function _prepareLayout()
    {
        $this->addChild('entity_items', 'Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Items');
        $this->addChild('cart_items', 'Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Cart');
        $this->addChild('sharing_form', 'Magento_GiftRegistry_Block_Adminhtml_Customer_Edit_Sharing');
        $this->addChild('update_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Update Items and Qty\'s'),
            'type'  => 'submit'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve website name
     *
     * @return string
     */
    public function getWebsiteName()
    {
        return $this->storeManager->getWebsite($this->getEntity()->getWebsiteId())->getName();
    }

    /**
     * Retrieve owner name
     *
     * @return string
     */
    public function getOwnerName()
    {
        $customer = $this->customerFactory->create()
            ->load($this->getEntity()->getCustomerId());

        return $this->escapeHtml($customer->getName());
    }

    /**
     * Retrieve customer edit form url
     *
     * @return string
     */
    public function getOwnerUrl()
    {
        return $this->getUrl('*/customer/edit', array('id' => $this->getEntity()->getCustomerId()));
    }

    /**
     * Retrieve gift registry type name
     *
     * @return string
     */
    public function getTypeName()
    {
        $type = $this->giftRegistryTypeFactory->create()
            ->load($this->getEntity()->getTypeId());

        return $this->escapeHtml($type->getLabel());
    }

   /**
     * Retrieve escaped entity title
     *
     * @return string
     */
    public function getEntityTitle()
    {
        return $this->escapeHtml($this->getEntity()->getTitle());
    }

   /**
     * Retrieve escaped entity message
     *
     * @return string
     */
    public function getEntityMessage()
    {
        return $this->escapeHtml($this->getEntity()->getMessage());
    }

   /**
     * Retrieve list of registrants
     *
     * @return string
     */
    public function getRegistrants()
    {
        return $this->escapeHtml($this->getEntity()->getRegistrants());
    }

   /**
     * Return gift registry entity object
     *
     * @return Magento_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return $this->_coreRegistry->registry('current_giftregistry_entity');
    }

   /**
     * Return shipping address
     *
     * @return Magento_GiftRegistry_Model_Entity
     */
    public function getShippingAddressHtml()
    {
        return $this->getEntity()->getFormatedShippingAddress();
    }

   /**
     * Return gift registry creation data
     *
     * @return Magento_GiftRegistry_Model_Entity
     */
    public function getCreatedAt()
    {
        return $this->formatDate($this->getEntity()->getCreatedAt(),
            Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true
        );
    }

    /**
     * Return update items form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/update', array('_current' => true));
    }
}

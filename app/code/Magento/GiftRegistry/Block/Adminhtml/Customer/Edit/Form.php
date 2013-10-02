<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Form
    extends \Magento\Adminhtml\Block\Widget\Form
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\GiftRegistry\Model\TypeFactory
     */
    protected $giftRegistryTypeFactory;

    protected $_template = 'customer/form.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\GiftRegistry\Model\TypeFactory $giftRegistryTypeFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\GiftRegistry\Model\TypeFactory $giftRegistryTypeFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
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
     * @return \Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Form
     */
    protected function _prepareLayout()
    {
        $this->addChild('entity_items', 'Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Items');
        $this->addChild('cart_items', 'Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Cart');
        $this->addChild('sharing_form', 'Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Sharing');
        $this->addChild('update_button', 'Magento\Adminhtml\Block\Widget\Button', array(
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
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return $this->_coreRegistry->registry('current_giftregistry_entity');
    }

   /**
     * Return shipping address
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getShippingAddressHtml()
    {
        return $this->getEntity()->getFormatedShippingAddress();
    }

   /**
     * Return gift registry creation data
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getCreatedAt()
    {
        return $this->formatDate($this->getEntity()->getCreatedAt(),
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true
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

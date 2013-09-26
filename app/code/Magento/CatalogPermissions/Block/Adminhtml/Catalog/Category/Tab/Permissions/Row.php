<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permissions row block
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
namespace Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions;

class Row
    extends \Magento\Adminhtml\Block\Catalog\Category\AbstractCategory
{

    protected $_template = 'catalog/category/tab/permissions/row.phtml';

    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupCollFactory;

    /**
     * @var \Magento\Core\Model\Resource\Website\CollectionFactory
     */
    protected $_websiteCollFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\Website\CollectionFactory $websiteCollFactory
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\Website\CollectionFactory $websiteCollFactory,
        \Magento\Customer\Model\Resource\Group\CollectionFactory $groupCollFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->_websiteCollFactory = $websiteCollFactory;
        $this->_groupCollFactory = $groupCollFactory;
        parent::__construct($coreData, $context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('delete_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            //'label' => __('Remove Permission'),
            'class' => 'delete' . ($this->isReadonly() ? ' disabled' : ''),
            'disabled' => $this->isReadonly(),
            'type'  => 'button',
            'id'    => '{{html_id}}_delete_button'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Check edit by websites
     *
     * @return boolean
     */
    public function canEditWebsites()
    {
        return !$this->_storeManager->hasSingleStore();
    }

    /**
     * Check is block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }

    public function getDefaultWebsiteId()
    {
        return $this->_storeManager->getStore(true)->getWebsiteId();
    }

    /**
     * Retrieve list of permission grants
     *
     * @return array
     */
    public function getGrants()
    {
        return array(
            'grant_catalog_category_view' => __('Browsing Category'),
            'grant_catalog_product_price' => __('Display Product Prices'),
            'grant_checkout_items' => __('Add to Cart')
        );
    }

    /**
     * Retrieve field class name
     *
     * @param string $fieldId
     * @return string
     */
    public function getFieldClassName($fieldId)
    {
        return strtr($fieldId, '_', '-') . '-value';
    }

    /**
     * Retrieve websites collection
     *
     * @return \Magento\Core\Model\Resource\Website\Collection
     */
    public function getWebsiteCollection()
    {
        if (!$this->hasData('website_collection')) {
            $collection = $this->_websiteCollFactory->create();
            $this->setData('website_collection', $collection);
        }

        return $this->getData('website_collection');
    }

    /**
     * Retrieve customer group collection
     *
     * @return \Magento\Customer\Model\Resource\Group\Collection
     */
    public function getCustomerGroupCollection()
    {
        if (!$this->hasData('customer_group_collection')) {
            $collection = $this->_groupCollFactory->create();
            $this->setData('customer_group_collection', $collection);
        }

        return $this->getData('customer_group_collection');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
}

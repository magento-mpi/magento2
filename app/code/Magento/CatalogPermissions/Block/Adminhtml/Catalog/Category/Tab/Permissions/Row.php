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

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Category\AbstractCategory;
use Magento\Core\Model\Registry;
use Magento\Catalog\Model\Resource\Category\Tree;
use Magento\Core\Model\Resource\Website\Collection as WebsiteCollection;
use Magento\Core\Model\Resource\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Customer\Model\Resource\Group\Collection as GroupCollection;
use Magento\Customer\Model\Resource\Group\CollectionFactory as GroupCollectionFactory;
use Magento\View\Element\AbstractBlock;

class Row extends AbstractCategory
{
    /**
     * @var string
     */
    protected $_template = 'catalog/category/tab/permissions/row.phtml';

    /**
     * @var GroupCollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * @var WebsiteCollectionFactory
     */
    protected $_websiteCollectionFactory;

    /**
     * @param Context $context
     * @param Tree $categoryTree
     * @param Registry $registry
     * @param WebsiteCollectionFactory $websiteCollectionFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Tree $categoryTree,
        Registry $registry,
        WebsiteCollectionFactory $websiteCollectionFactory,
        GroupCollectionFactory $groupCollectionFactory,
        array $data = array()
    ) {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        $this->_groupCollectionFactory = $groupCollectionFactory;
        parent::__construct($context, $categoryTree, $registry, $data);
    }

    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('delete_button', 'Magento\Backend\Block\Widget\Button', array(
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
     * @return bool
     */
    public function canEditWebsites()
    {
        return !$this->_storeManager->hasSingleStore();
    }

    /**
     * Check is block readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }

    /**
     * @return string|int|null
     */
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
     * @return WebsiteCollection
     */
    public function getWebsiteCollection()
    {
        if (!$this->hasData('website_collection')) {
            $collection = $this->_websiteCollectionFactory->create();
            $this->setData('website_collection', $collection);
        }

        return $this->getData('website_collection');
    }

    /**
     * Retrieve customer group collection
     *
     * @return GroupCollection
     */
    public function getCustomerGroupCollection()
    {
        if (!$this->hasData('customer_group_collection')) {
            $collection = $this->_groupCollectionFactory->create();
            $this->setData('customer_group_collection', $collection);
        }

        return $this->getData('customer_group_collection');
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
}

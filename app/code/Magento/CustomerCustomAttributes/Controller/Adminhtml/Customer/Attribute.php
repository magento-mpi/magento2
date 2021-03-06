<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer;

/**
 * Controller for Customer Attributes Management
 */
class Attribute extends \Magento\Backend\App\Action
{
    /**
     * Customer Address Entity Type instance
     *
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $_attrFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $_attrSetFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\AttributeFactory $attrFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attrSetFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\AttributeFactory $attrFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attrSetFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_eavConfig = $eavConfig;
        $this->_attrFactory = $attrFactory;
        $this->_attrSetFactory = $attrSetFactory;
        parent::__construct($context);
    }

    /**
     * Return Customer Address Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = $this->_eavConfig->getEntityType('customer');
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Backend::stores_attributes'
        )->_addBreadcrumb(
            __('Customer'),
            __('Customer')
        )->_addBreadcrumb(
            __('Manage Customer Attributes'),
            __('Manage Customer Attributes')
        );
        return $this;
    }

    /**
     * Retrieve customer attribute object
     *
     * @return \Magento\Customer\Model\Attribute
     */
    protected function _initAttribute()
    {
        /** @var $attribute \Magento\Customer\Model\Attribute */
        $attribute = $this->_attrFactory->create();
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }

    /**
     * Check whether attributes management functionality is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CustomerCustomAttributes::customer_attributes');
    }
}

<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item;

class Attribute extends \Magento\Backend\App\Action
{
    /**
     * RMA Item Entity Type instance
     *
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Return RMA Item Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = $this->_objectManager->get('Magento\Eav\Model\Config')->getEntityType('rma_item');
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Rma::sales_magento_rma_rma_item_attribute'
        )->_addBreadcrumb(
            __('RMA'),
            __('RMA')
        )->_addBreadcrumb(
            __('Manage RMA Item Attributes'),
            __('Manage RMA Item Attributes')
        );
        return $this;
    }

    /**
     * Retrieve RMA item attribute object
     *
     * @return \Magento\Rma\Model\Item\Attribute
     */
    protected function _initAttribute()
    {
        /** @var $attribute \Magento\Rma\Model\Item\Attribute */
        $attribute = $this->_objectManager->create('Magento\Rma\Model\Item\Attribute');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }

    /**
     * Check the permission
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rma::rma_attribute');
    }
}

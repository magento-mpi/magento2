<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\Backend\App\Action;

/**
 * Custom Variables admin controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Variable extends Action
{
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
     * Initialize Layout and set breadcrumbs
     *
     * @return $this
     */
    protected function _initLayout()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Backend::system_variable'
        )->_addBreadcrumb(
            __('Custom Variables'),
            __('Custom Variables')
        );
        return $this;
    }

    /**
     * Initialize Variable object
     *
     * @return \Magento\Core\Model\Variable
     */
    protected function _initVariable()
    {
        $this->_title->add(__('Custom Variables'));

        $variableId = $this->getRequest()->getParam('variable_id', null);
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        /* @var $variable \Magento\Core\Model\Variable */
        $variable = $this->_objectManager->create('Magento\Core\Model\Variable');
        if ($variableId) {
            $variable->setStoreId($storeId)->load($variableId);
        }
        $this->_coreRegistry->register('current_variable', $variable);
        return $variable;
    }

    /**
     * Check current user permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::variable');
    }
}

<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Model\Exception;

class Giftregistry extends \Magento\Backend\App\Action
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
     * Init active menu and set breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_GiftRegistry::customer_magento_giftregistry'
        )->_addBreadcrumb(
            __('Gift Registry'),
            __('Gift Registry')
        );

        $this->_title->add(__('Gift Registry Types'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return \Magento\GiftRegistry\Model\Type
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initType($requestParam = 'id')
    {
        $type = $this->_objectManager->create('Magento\GiftRegistry\Model\Type');
        $type->setStoreId($this->getRequest()->getParam('store', 0));

        $typeId = $this->getRequest()->getParam($requestParam);
        if ($typeId) {
            $type->load($typeId);
            if (!$type->getId()) {
                throw new Exception(__('Please correct the  gift registry ID.'));
            }
        }
        $this->_coreRegistry->register('current_giftregistry_type', $type);
        return $type;
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }
}

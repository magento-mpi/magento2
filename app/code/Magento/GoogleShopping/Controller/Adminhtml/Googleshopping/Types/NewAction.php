<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Types;

class NewAction extends \Magento\GoogleShopping\Controller\Adminhtml\Googleshopping\Types
{
    /**
     * Create new attribute set mapping
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initItemType();

            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Google Content Attribute Mapping'));

            $this->_initAction()->_addBreadcrumb(
                __('New attribute set mapping'),
                __('New attribute set mapping')
            )->_addContent(
                $this->_view->getLayout()->createBlock('Magento\GoogleShopping\Block\Adminhtml\Types\Edit')
            );
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError(__("We can't create Attribute Set Mapping."));
            $this->_redirect('adminhtml/*/index', array('store' => $this->_getStore()->getId()));
        }
    }
}

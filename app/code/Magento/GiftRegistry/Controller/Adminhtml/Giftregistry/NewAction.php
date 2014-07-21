<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

use \Magento\Framework\Model\Exception;

class NewAction extends \Magento\GiftRegistry\Controller\Adminhtml\Giftregistry
{
    /**
     * Create new gift registry type
     *
     * @return void
     */
    public function execute()
    {
        try {
            $model = $this->_initType();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/');
            return;
        }

        $this->_initAction();
        $this->_title->add(__('New Gift Registry Type'));

        $block = $this->_view->getLayout()->createBlock(
            'Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit'
        )->setData(
            'form_action_url',
            $this->getUrl('adminhtml/*/save')
        );

        $this->_addBreadcrumb(
            __('New Type'),
            __('New Type')
        )->_addContent(
            $block
        )->_addLeft(
            $this->_view->getLayout()->createBlock('Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tabs')
        );
        $this->_view->renderLayout();
    }
}

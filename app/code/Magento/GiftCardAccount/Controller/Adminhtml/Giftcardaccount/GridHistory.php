<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount;

class GridHistory extends \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount
{
    /**
     * Render GCA history grid
     *
     * @return void
     */
    public function execute()
    {
        $model = $this->_initGca();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id && !$model->getId()) {
            return;
        }

        $this->_view->loadLayout();
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\GiftCardAccount\Block\Adminhtml\Giftcardaccount\Edit\Tab\History'
            )->toHtml()
        );
    }
}

<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Controller\Adminhtml\Pbridge;

class Iframe extends \Magento\Pbridge\Controller\Adminhtml\Pbridge
{
    /**
     * Iframe Ajax Action
     *
     *  @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = $this->_objectManager->get(
                'Magento\Payment\Helper\Data'
            )->getMethodInstance(
                $methodCode
            );
            if ($methodInstance) {
                $block = $this->_view->getLayout()->createBlock($methodInstance->getFormBlockType());
                $block->setMethod($methodInstance);
                if ($this->getRequest()->getParam('data')) {
                    $block->setFormParams($this->getRequest()->getParam('data', null));
                }
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            throw new \Magento\Framework\Model\Exception(__('Payment Method Code is not passed.'));
        }
    }
}

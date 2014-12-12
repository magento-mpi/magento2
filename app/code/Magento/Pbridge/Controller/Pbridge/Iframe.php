<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Iframe extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Iframe Ajax Action
     *
     * @return void
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
            $block = $this->_view->getLayout()->createBlock($methodInstance->getFormBlockType());
            $block->setMethod($methodInstance);
            if ($this->getRequest()->getParam('data')) {
                $block->setFormParams($this->getRequest()->getParam('data', null));
            }
            if ($block) {
                $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
            }
        } else {
            throw new \Magento\Framework\Model\Exception(__('Payment Method Code is not passed.'));
        }
    }
}

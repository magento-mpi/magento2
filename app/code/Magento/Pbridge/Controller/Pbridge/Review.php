<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Controller\Pbridge;

class Review extends \Magento\Pbridge\Controller\Pbridge
{
    /**
     * Iframe Ajax Action for review page
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
            $block = $this->_view->getLayout()->createBlock(
                'Magento\Pbridge\Block\Checkout\Payment\Review\Iframe'
            );
            $block->setMethod($methodInstance);
            if ($block) {
                $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
            }
        } else {
            throw new \Magento\Framework\Model\Exception(__('Payment Method Code is not passed.'));
        }
    }
}

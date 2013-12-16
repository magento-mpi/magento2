<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index controller
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Controller\Adminhtml;

class Pbridge extends \Magento\Backend\App\Action
{
    /**
     * Load only action layout handles
     *
     * @return \Magento\Pbridge\Controller\Adminhtml\Pbridge
     */
    protected function _initActionLayout()
    {
        $this->_view->addActionLayoutHandles();
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $this->_view->setIsLayoutLoaded(true);
        $this->_view->getLayout()->initMessages();
        return $this;
    }

    /**
     * Index Action.
     * Forward to result action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('result');
    }

    /**
     * Iframe Ajax Action
     *
     *  @return void
     * @throws \Magento\Core\Exception
     */
    public function iframeAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = $this->_objectManager->get('Magento\Payment\Helper\Data')->getMethodInstance($methodCode);
            if ($methodInstance) {
                $block = $this->_view->getLayout()->createBlock($methodInstance->getFormBlockType());
                $block->setMethod($methodInstance);
                if($this->getRequest()->getParam('data')) {
                    $block->setFormParams($this->getRequest()->getParam('data', null));
                }
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            throw new \Magento\Core\Exception(__('Payment Method Code is not passed.'));
        }
    }

    /**
     * Result Action
     *
     * @return void
     */
    public function resultAction()
    {
        if ($this->getRequest()->getParam('store')) {
            $this->_objectManager->get('Magento\Pbridge\Helper\Data')->setStoreId($this->getRequest()->getParam('store'));
        }
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_order');
    }
}

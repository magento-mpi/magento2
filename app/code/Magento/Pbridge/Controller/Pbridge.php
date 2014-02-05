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
namespace Magento\Pbridge\Controller;

class Pbridge extends \Magento\App\Action\Action
{
    /**
     * Load only action layout handles
     *
     * @return \Magento\Pbridge\Controller\Pbridge
     */
    protected function _initActionLayout()
    {
        $this->_view->addActionLayoutHandles();
        $this->_view->loadLayoutUpdates();
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
        $this->_view->setIsLayoutLoaded(true);
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
     * @return void
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
     * Iframe Ajax Action for review page
     *
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function reviewAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = $this->_objectManager->get('Magento\Payment\Helper\Data')->getMethodInstance($methodCode);
            if ($methodInstance) {
                $block = $this->_view->getLayout()->createBlock('Magento\Pbridge\Block\Checkout\Payment\Review\Iframe');
                $block->setMethod($methodInstance);
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            throw new \Magento\Core\Exception(__('Payment Method Code is not passed.'));
        }
    }

    /**
     * Review success action
     *
     * @return void
     */
    public function successAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Review error action
     *
     * @return void
     */
    public function errorAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Result Action
     *
     * @return void
     */
    public function resultAction()
    {
        $this->_initActionLayout();
        $this->_view->renderLayout();
    }

    /**
     * Validate all agreements
     * (terms and conditions are agreed)
     *
     * @return void
     */
    public function validateAgreementAction()
    {
        $result = array();
        $result['success'] = true;
        $requiredAgreements = $this->_objectManager->get('Magento\Checkout\Helper\Data')->getRequiredAgreementIds();
        if ($requiredAgreements) {
            $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
            $diff = array_diff($requiredAgreements, $postedAgreements);
            if ($diff) {
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = __('Please agree to all the terms and conditions before placing the order.');
            }
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }
}

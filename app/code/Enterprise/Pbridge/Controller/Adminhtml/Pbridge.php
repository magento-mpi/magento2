<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Index controller
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Controller_Adminhtml_Pbridge extends Magento_Adminhtml_Controller_Action
{
    /**
     * Load only action layout handles
     *
     * @return Enterprise_Pbridge_Adminhtml_IndexController
     */
    protected function _initActionLayout()
    {
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');
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
     */
    public function iframeAction()
    {
        $methodCode = $this->getRequest()->getParam('method_code', null);
        if ($methodCode) {
            $methodInstance = Mage::helper('Magento_Payment_Helper_Data')->getMethodInstance($methodCode);
            if ($methodInstance) {
                $block = $this->getLayout()->createBlock($methodInstance->getFormBlockType());
                $block->setMethod($methodInstance);
                if($this->getRequest()->getParam('data')) {
                    $block->setFormParams($this->getRequest()->getParam('data', null));
                }
                if ($block) {
                    $this->getResponse()->setBody($block->getIframeBlock()->toHtml());
                }
            }
        } else {
            Mage::throwException(__('Payment Method Code is not passed.'));
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
            Mage::helper('Enterprise_Pbridge_Helper_Data')->setStoreId($this->getRequest()->getParam('store'));
        }
        $this->_initActionLayout();
        $this->renderLayout();
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

<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Index controller
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PBridge_IndexController extends Enterprise_Enterprise_Controller_Core_Front_Action
{
    /**
     * Load only action layout handles
     *
     * @return Enterprise_PBridge_IndexController
     */
    protected function _initActionLayout()
    {
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        return $this;
    }

    /**
     * Initialize incoming data required to use Payment Bridge payment method
     *
     * @return array
     */
    protected function _initIncomingData()
    {
        $data = array(
            'original_payment_method' => $this->getRequest()->getParam('original_payment_method', null),
            'token' => $this->getRequest()->getParam('token'),
            'quote_id' => $this->getRequest()->getParam('quote_id')
        );
        return $data;
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
     * Result Action
     *
     * @return void
     */
    public function resultAction()
    {
        $data = $this->_initIncomingData();
        $this->_initActionLayout();

        /**
         * @todo create hidden input fields dynamically
         */
        $originalPaymentMethod = $this->getRequest()->getParam('original_payment_method');
        $block = $this->getLayout()->getBlock('pridge.checkout.payment.result');
        $block->setOriginalPaymentMethodCode($originalPaymentMethod);

        $this->renderLayout();
    }
}

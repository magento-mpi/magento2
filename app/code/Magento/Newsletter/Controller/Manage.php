<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customers newsletter subscription controller
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Controller;

class Manage extends \Magento\Core\Controller\Front\Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('\Magento\Customer\Model\Session');
        $this->_initLayoutMessages('\Magento\Catalog\Model\Session');

        if ($block = $this->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle(__('Newsletter Subscription'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()
            ->setStoreId(\Mage::app()->getStore()->getId())
            ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
            ->save();
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                \Mage::getSingleton('Magento\Customer\Model\Session')->addSuccess(__('We saved the subscription.'));
            } else {
                \Mage::getSingleton('Magento\Customer\Model\Session')->addSuccess(__('We removed the subscription.'));
            }
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError(__('Something went wrong while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}

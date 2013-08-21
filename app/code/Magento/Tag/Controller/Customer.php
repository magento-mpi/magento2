<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer tags controller
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Controller_Customer extends Magento_Core_Controller_Front_Action
{
    protected function _getTagId()
    {
        $tagId = (int) $this->getRequest()->getParam('tagId');
        if ($tagId) {
            $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
            $model = Mage::getModel('Magento_Tag_Model_Tag_Relation');
            $model->loadByTagCustomer(null, $tagId, $customerId);
            Mage::register('tagModel', $model);
            return $model->getTagId();
        }
        return false;
    }

    public function indexAction()
    {
        if( !Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn() ) {
            Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Tag_Model_Session');
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('tag/customer');
        }

        $block = $this->getLayout()->getBlock('customer_tags');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle(__('My Tags'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        if( !Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn() ) {
            Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this);
            return;
        }

        $tagId = $this->_getTagId();
        if ($tagId) {
            Mage::register('tagId', $tagId);
            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Tag_Model_Session');

            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('tag/customer');
            }

            $this->_initLayoutMessages('Magento_Checkout_Model_Session');
            $this->getLayout()->getBlock('head')->setTitle(__('My Tags'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function removeAction()
    {
        if( !Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn() ) {
            Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this);
            return;
        }

        $tagId = $this->_getTagId();
        if ($tagId) {
            try {
                $model = Mage::registry('tagModel');
                $model->deactivate();

                Mage::getSingleton('Magento_Tag_Model_Session')->addSuccess(
                    __('You deleted the tag.')
                );
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/', array(
                    self::PARAM_NAME_URL_ENCODED => Mage::helper('Magento_Core_Helper_Data')->urlEncode(
                        Mage::getUrl('customer/account/')
                    )
                )));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Tag_Model_Session')->addError(__('We can\'t remove the tag. Please try again later.'));
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }
}

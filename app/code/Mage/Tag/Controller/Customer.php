<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer tags controller
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Controller_Customer extends Mage_Core_Controller_Front_Action
{
    protected function _getTagId()
    {
        $tagId = (int) $this->getRequest()->getParam('tagId');
        if ($tagId) {
            $customerId = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId();
            $model = Mage::getModel('Mage_Tag_Model_Tag_Relation');
            $model->loadByTagCustomer(null, $tagId, $customerId);
            Mage::register('tagModel', $model);
            return $model->getTagId();
        }
        return false;
    }

    public function indexAction()
    {
        if( !Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn() ) {
            Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Tag_Model_Session');
        $this->_initLayoutMessages('Mage_Catalog_Model_Session');

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
        if( !Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn() ) {
            Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this);
            return;
        }

        $tagId = $this->_getTagId();
        if ($tagId) {
            Mage::register('tagId', $tagId);
            $this->loadLayout();
            $this->_initLayoutMessages('Mage_Tag_Model_Session');

            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('tag/customer');
            }

            $this->_initLayoutMessages('Mage_Checkout_Model_Session');
            $this->getLayout()->getBlock('head')->setTitle(__('My Tags'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    public function removeAction()
    {
        if( !Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn() ) {
            Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this);
            return;
        }

        $tagId = $this->_getTagId();
        if ($tagId) {
            try {
                $model = Mage::registry('tagModel');
                $model->deactivate();

                Mage::getSingleton('Mage_Tag_Model_Session')->addSuccess(
                    __('You deleted the tag.')
                );
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/', array(
                    self::PARAM_NAME_URL_ENCODED => Mage::helper('Mage_Core_Helper_Data')->urlEncode(
                        Mage::getUrl('customer/account/')
                    )
                )));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Tag_Model_Session')->addError(__('We can\'t remove the tag. Please try again later.'));
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }
}

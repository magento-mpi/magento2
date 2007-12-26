<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @module     Catalog
 */
class Mage_Catalog_ProductController extends Mage_Core_Controller_Front_Action
{
    protected function _initProduct()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')
            ->load($productId);

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            Mage::register('current_category', $category);
        }
        Mage::register('current_product', $product);
        Mage::register('product', $product); // this need remove after all replace
    }

	public function viewAction()
    {
        $this->_initProduct();
        $product = Mage::registry('product');
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_forward('noRoute');
            return;
        }

        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_'.$product->getId());

        $this->loadLayoutUpdates();

        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml()->generateLayoutBlocks();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('tag/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function galleryAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sendAction(){
        $this->_initProduct();
        $product = Mage::registry('product');
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_forward('noRoute');
            return;
        }

        // check if user is allowed to send product to a friend
        $productHelper = Mage::helper('catalog/product');
        /* @var $productHelper Mage_Catalog_Helper_Product */
        if (! $productHelper->canEmailToFriend()) {
            Mage::getSingleton('catalog/session')->addError($this->__('You cannot email this product to a friend'));
            $this->_redirectReferer($productHelper->getProductUrl());
            return;
        }

        $maxSendsToFriend = $productHelper->getMaxSendsToFriend();
        if ($maxSendsToFriend){
            Mage::getSingleton('catalog/session')->addNotice($this->__('You cannot send more than %d times in an hour', $maxSendsToFriend));
        }

        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        $this->addActionLayoutHandles();

        $update->addHandle('PRODUCT_'.$product->getId());

        $this->loadLayoutUpdates();

        $update->addUpdate($product->getCustomLayoutUpdate());

        $this->generateLayoutXml()->generateLayoutBlocks();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('tag/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function sendmailAction()
    {
        // check if user is allowed to send product to a friend
        $productHelper = Mage::helper('catalog/product');
        /* @var $productHelper Mage_Catalog_Helper_Product */
        if (! $productHelper->canEmailToFriend()) {
            Mage::getSingleton('catalog/session')->addError($this->__('You cannot email this product to a friend'));
            $this->_redirectReferer($productHelper->getProductUrl());
            return;
        }

        $recipients_email = array();

        $maxRecipients = $productHelper->getMaxRecipients();
        $maxSendsToFriend = $productHelper->getMaxSendsToFriend();

        if($this->getRequest()->getPost() && $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
              ->load((int)$this->getRequest()->getParam('id'));


            $sendToFriendModel = Mage::getModel('catalog/sendfriend_log');
            $startTime = time()-60*60;

            if ($productHelper->getSendToFriendCheckType()){
                // cookie check
                $newTimes = array();
                $oldTimes = Mage::getSingleton('core/cookie')->get('stf');
                if ($oldTimes){
                    $oldTimes = explode(',', $oldTimes);
                    foreach ($oldTimes as $time){
                        if (is_numeric($time) && $time >= $startTime){
                            $newTimes[] = $time;
                        }
                    }
                }
                $amount = count($newTimes);

                if ($amount >= $maxSendsToFriend){
                    Mage::getSingleton('catalog/session')->addError(Mage::helper('catalog')->__('You have exceeded limit of %d sends in an hour', $maxSendsToFriend));
                    $this->_redirectError(Mage::getURL('catalog/product/send',array('id'=>$product->getId())));
                    return;
                }

                $newTimes[] = time();
                Mage::getSingleton('core/cookie')->set('stf', implode(',', $newTimes), 60*60);
            } else {
                // ip db check
                $visitorModel = Mage::getModel('log/visitor');

                $visitorModel->initServerData();
                $ip = $visitorModel->getRemoteAddr();

                $sendToFriendModel->deleteLogsBefore($startTime);

                $amount = $sendToFriendModel->getSendCount($ip, $startTime);

                if ($amount >= $maxSendsToFriend){

                    Mage::getSingleton('catalog/session')->addError(Mage::helper('catalog')->__('You have exceeded limit of %d sends in an hour', $maxSendsToFriend));
                    $this->_redirectError(Mage::getURL('catalog/product/send',array('id'=>$product->getId())));
                    return;
                }

                $sendToFriendModel->setData(array('ip'=>$ip, 'time'=>time()));
                try {
                    $sendToFriendModel->save();
                } catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('catalog/session')->addError(Mage::helper('catalog')->__($e->getMessage()));
                    $this->_redirectError(Mage::getURL('catalog/product/send',array('id'=>$product->getId())));
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('catalog/session')->addException($e, Mage::helper('catalog')->__('Database error occured'));
                    $this->_redirectError(Mage::getURL('catalog/product/send',array('id'=>$product->getId())));
                    return;
                }
            }

            $sender = $this->getRequest()->getParam('sender');
            $recipients = $this->getRequest()->getParam('recipients');
            $recipients_email = $recipients['email'];
            $recipients_email = array_unique($recipients_email);
            $recipients_name = $recipients['name'];

            if ($maxRecipients && count($recipients_email) > $maxRecipients) {
                Mage::getSingleton('catalog/session')->addError(
                  Mage::helper('catalog')->__('You cannot send more than %d emails at a time', $maxRecipients)
                );

                $this->_redirectError(Mage::getURL('catalog/product/send',array('id'=>$product->getId())));

                return false;
            }

            $errors = array();
            foreach ($recipients_email as $key=>$emailTo) {
                if($emailTo){
                    $emailModel = Mage::getModel('core/email_template');
                    $emailTo = trim($emailTo);
                    $recipient = $recipients_name[$key];
                    $templ = Mage::getStoreConfig('sendfriend/email/template');
                    if(!$templ){

                        return false;
                    }
                	$emailModel->load(Mage::getStoreConfig('sendfriend/email/template'));
                	if (!$emailModel->getId()) {
                		 Mage::getSingleton('catalog/session')->addError($this->__('Invalid transactional email code'));
                	}
                	$emailModel->setSenderName(strip_tags($sender['name']));
                	$emailModel->setSenderEmail(strip_tags($sender['email']));

                	$vars = array(
                	   'senderName' => strip_tags($sender['name']),
                	   'senderEmail' => strip_tags($sender['email']),
                	   'receiverName' => strip_tags($recipient),
                	   'receiverEmail' => strip_tags($emailTo),
                	   'product' => $product,
                	   'message' => strip_tags($sender['message'])
                	   );
                	if(!$emailModel->send(strip_tags($emailTo), strip_tags($recipient), $vars)){
                	    $errors[] = $emailTo;
                	}

                }
            }
            if(count($errors)>0){
                foreach ($errors as $val) {
                    Mage::getSingleton('catalog/session')->addError($this->__('Email to %s does not sent.'),$val);
                }
                $this->_redirectError(Mage::getURL('catalog/product/send',array('id'=>$product->getId())));
            } else {
                Mage::getSingleton('catalog/session')->addSuccess($this->__('Link to a friend was sent.'));
                $this->_redirectSuccess($product->getProductUrl());
            }
        }
    }
}

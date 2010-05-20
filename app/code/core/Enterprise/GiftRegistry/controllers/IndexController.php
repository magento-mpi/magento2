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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry frontend controller
 */
class Enterprise_GiftRegistry_IndexController extends Enterprise_Enterprise_Controller_Core_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * View giftregistry list in 'My Account' section
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        if ($block = $this->getLayout()->getBlock('giftregistry_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    /**
     * Add quote items to customer active gift registry
     */
    public function cartAction()
    {
        $entity = $this->_getActiveEntity();
        $count  = 0;

        try {
            if ($entity && $entity->getId()) {
                $quote = Mage::getSingleton('checkout/cart')->getQuote();
                foreach ($quote->getAllVisibleItems() as $item) {
                    $entity->addItem($item);
                    $count += $item->getQty();
                }
                if ($count > 0) {
                    Mage::getSingleton('checkout/session')->addSuccess(
                        Mage::helper('enterprise_giftregistry')->__('%d shopping cart items have been added to gift registry.', $count)
                    );
                } else {
                    Mage::getSingleton('checkout/session')->addNotice(
                        Mage::helper('enterprise_giftregistry')->__('Nothing to add to gift registry.')
                    );
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('checkout/session')->addError($this->__('Failed to add shopping cart items to gift registry.'));
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Add wishlist items to customer active gift registry action
     */
    public function wishlistAction()
    {
        if ($items = $this->getRequest()->getParam('items')) {
            $entity = $this->_getActiveEntity();
            try {
                if ($entity && $entity->getId() && is_array($items)) {
                    foreach (array_keys($items) as $item) {
                        $entity->addItem((int)$item);
                    }
                    if (count($items) > 0) {
                        $this->_getSession()->addSuccess(
                            Mage::helper('enterprise_giftregistry')->__('%d wishlist items have been added to gift registry.', count($items))
                        );
                    } else {
                        $this->_getSession()->addNotice(
                            Mage::helper('enterprise_giftregistry')->__('Nothing to add to gift registry.')
                        );
                    }
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Failed to add wishlist items to gift registry.'));
            }
        }

        $this->_redirect('wishlist');
    }

    /**
     * Update gift registry list, set selected entity as active
     */
    public function updateAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        $active = $this->getRequest()->getParam('active');
        $entity = $this->_initEntity()->load($active);
        try {
            $customerId = $this->_getSession()->getCustomerId();

            if ($entity->getId() && $entity->getCustomerId() == $customerId) {
                $entity->setActiveEntity($customerId, (int)$active);
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_giftregistry')->__('Gift registry list has been updated successfully.')
                );
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Failed to update gift registry list.'));
        }

        $this->_redirect('giftregistry');
    }

    /**
     * Delete selected gift registry entity
     *
     */
    public function deleteAction()
    {
        $entity = $this->_initEntity();
        if ($entity->getId()) {
            try {
                $customerId = $this->_getSession()->getCustomerId();
                if ($entity->getId() && $entity->getCustomerId() == $customerId) {
                    $entity->delete();
                    $this->_getSession()->addSuccess(
                        Mage::helper('enterprise_giftregistry')->__('Gift registry entity has been deleted.')
                    );
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e);
            }
        }
        $this->_redirect('giftregistry');
    }

    /**
     * Share selected gift registry entity
     */
    public function shareAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('giftregistry.customer.share')
            ->setEntity($this->_initEntity());
        $this->renderLayout();
    }

    /**
     * View items of selected gift registry entity
     */
    public function itemsAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('giftregistry.customer.items')
            ->setEntity($this->_initEntity());
        $this->renderLayout();
    }

    /**
     * Update gift registry items
     */
    public function updateItemsAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $entity = $this->_initEntity();
        $customerId = $this->_getSession()->getCustomerId();

        if ($entity->getId() && $entity->getCustomerId() == $customerId) {
            $items = $this->getRequest()->getParam('items');
            try {
                foreach ($items as $id => $item) {
                    $model = Mage::getModel('enterprise_giftregistry/item')->load($id);

                    if ($model->getId() && $model->getEntityId() == $entity->getId()) {
                        if (isset($item['delete'])) {
                            $model->delete();
                        } else {
                            $model->setQty($item['qty']);
                            $model->setNote($item['note']);
                            $model->save();
                        }
                    } else {
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Gift registry item is not longer exists.'));
                    }
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_giftregistry')->__('The gift registry items have been updated.')
                );
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('Failed to update gift registry items list.'));
            }
        }

        $this->_redirect('*/*/items', array('_current' => true));
    }
    /**
     * Share selected gift registry entity
     */
    public function sendAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/share', array('_current' => true));
            return;
        }

        $error  = false;
        $senderMessage = nl2br(htmlspecialchars($this->getRequest()->getPost('sender_message')));
        $senderName = htmlspecialchars($this->getRequest()->getPost('sender_name'));
        $senderEmail = htmlspecialchars($this->getRequest()->getPost('sender_email'));

        if (!empty($senderName) && !empty($senderMessage) && !empty($senderEmail)) {
            if (Zend_Validate::is($senderEmail, 'EmailAddress')) {
                $emails = array();
                $recipients = $this->getRequest()->getPost('recipients');
                foreach ($recipients as $recipient) {
                    $recipientEmail = trim($recipient['email']);
                    if (!Zend_Validate::is($recipientEmail, 'EmailAddress')) {
                        $error = Mage::helper('enterprise_giftregistry/data')->__('Please input a valid recipient email address.');
                        break;
                    }

                    $recipient['name'] = htmlspecialchars($recipient['name']);
                    if (empty($recipient['name'])) {
                        $error = Mage::helper('enterprise_giftregistry/data')->__('Please input a recipient name.');
                        break;
                    }
                    $emails[] = $recipient;
                }

                $count = 0;
                if (count($emails) && !$error){
                    foreach($emails as $recipient) {
                        $sender = array('name' => $senderName, 'email' => $senderEmail);
                        if ($this->_initEntity()->sendShareEmail($recipient, null, $senderMessage, $sender)) {
                            $count++;
                        }
                    }
                    if ($count > 0) {
                        $this->_getSession()->addSuccess(
                            Mage::helper('enterprise_giftregistry')->__('The gift registry has been shared for %d emails.', $count)
                        );
                    } else {
                        $this->_getSession()->addNotice(
                            Mage::helper('enterprise_giftregistry')->__('Failed to share gift registry.')
                        );
                    }
                }

            } else {
                $error = Mage::helper('enterprise_giftregistry/data')->__('Please input a valid sender email address.');
            }
        } else {
            $error = Mage::helper('enterprise_giftregistry/data')->__('Sender data can\'t be empty.');
        }

        if ($error) {
            $this->_getSession()->addError($error);
            $this->_getSession()->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share', array('_current' => true));
            return;
        }
        $this->_redirect('*/*/index', array('_current' => true));
    }
    /**
     * Get current customer session
     *
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    protected function _getActiveEntity()
    {
        return Mage::getModel('enterprise_giftregistry/entity')
            ->getActiveEntity($this->_getSession()->getCustomerId());
    }

    /**
     * Get current customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        if ($block = $this->getLayout()->getBlock('giftregistry_view')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function addSelectAction()
    {
        $this->loadLayout();
        if ($block = $this->getLayout()->getBlock('giftregistry_addselect')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    /**
     * Select Type action
     */
    public function editAction()
    {
        $typeId = $this->getRequest()->getParam('type_id');
        if (!$typeId) {
            $data = $this->_getSession()->getGiftRegistryEntityFormData(true);
///            if ()
            $this->_redirect('*/*/addselect');
            return ;
        }
        $this->_initLayoutMessages('customer/session');

        /* @var $model Enterprise_GiftRegistry_Model_Entity */
        $model = Mage::getSingleton('enterprise_giftregistry/entity');
        $model->setTypeId($typeId);
        $data = $this->_getSession()->getGiftRegistryEntityFormData(true);
        if ($data) {
            $model->addData($data);
        }

        Mage::register('enterprise_giftregistry_entity', $model);

        $address = Mage::getModel('customer/address');
        $model->exportAddress($address);

        Mage::register('enterprise_giftregistry_address', $address);

        $this->loadLayout();

        $this->renderLayout();
    }

    /**
     * Create Registry action
     */
    public function editPostAction()
    {
        Mage::log('GiftRegistry: indexConts addPostAction');
        if (!($typeId = $this->getRequest()->getParam('type_id'))) {
            Mage::log('contr: AddpostAction validate false ');
//            $this->addAction($typeId);
            Mage::log('1 type id = '. $typeId);
            $this->_redirect('*/*/addselect');
            return;
        }

        if (!$this->_validateFormKey()) {
            Mage::log('contr: AddpostAction validate false ');
            $this->_redirect('*/*/add', array('type_id', $typeId));
            return ;
        }

        if ($this->getRequest()->isPost() && ($data = $this->getRequest()->getPost())) {
            Mage::log('addPostAction : isPost == TRUE ');
            Mage::log($data);

            $isError = false;

            try {
                Mage::log('Try: start');

                $model = Mage::getSingleton('enterprise_giftregistry/entity' );
                $model->setTypeId($typeId);
                $model->addData(array('attributes' => $this->getRequest()->getParam('attributes')));
                $model->addData(array(
                    'region' => $this->getRequest()->getParam('attributes'),
                    'event_date' => $this->getRequest()->getParam('event_date'),
                    'event_location' => $this->getRequest()->getParam('event_location'),
                    'country_id' => $this->getRequest()->getParam('country_id'),
                ));

                $model->addData(array(
                    'type_id' => $typeId,
                    'customer_id' => Mage::getSingleton('customer/session')->getCustomer()->getId(),
                    'website_id' => '0',
                    'is_public' => '1',
                    'url_key' => '/reg/1',
                    'title' => $this->getRequest()->getParam('title'),
                    'message' => $this->getRequest()->getParam('message'),
                    'shipping_address' => '12',
                    'custom_values' => serialize($this->getRequest()->getParam('attributes'))
                ));

                Mage::log('Try: set TypeID = '.$typeId);

                $addressType    = $this->getRequest()->getParam('address_type');
                switch ($addressType) {
                    case 'customer':
                        /* @var $customer Mage_Customer_Model_Customer */
                        $customer  = Mage::getSingleton('customer/session')->getCustomer();
                        $addressId = $this->getRequest()->getParam('shipping_address_id');
                        if (!$addressId) {
                            Mage::throwException('No address selected.');
                        }
                        $address = $customer->getAddressItemById($addressId);
                        if (!$address) {
                            Mage::throwException('Incorrect address selected.');
                        }
                        $model->importAddress($address);
                        break;
                    case 'new':
                        /* @var $address Mage_Customer_Model_Address */
                        $address = Mage::getModel('customer/address');
                        $address->setData($this->getRequest()->getParam('address'));
                        $errors = $address->validate();
                        if ($errors !== true) {
                            foreach ($errors as $err) {
                                $this->_getSession()->addError($err);
                            }
                            $isError = true;
                        }
                        break;
                    case 'none':
                    default:
                        break;

                }
//                $address = Mage::getModel('customer/address');
//
//                $address->
//                = $this->getRequest()->getParam('attributes');

                if (!$isError) {
                    $model->save();

                    $this->_getSession()->addSuccess(
                        Mage::helper('enterprise_giftregistry/data')->__('Data saved succesfully.')
                    );
                }
//                'static_registrant[]'
//                $_staticTypes = array('event_date', 'event_country_code', 'event_region_code', 'event_location');

//                Mage::log('Try: save ');
//                Mage::getSingleton('customer/session')->addSuccess($this->__('The gift registry type has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $isError = true;
//                $this->_redirect('*/*/add', array('type_id' => $typeId));
//                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('enterprise_giftregistry/data')->__('Errors found.')
                );
                $isError = true;
//                Mage::getSingleton('customer/session')->addError($this->__('Failed to save gift registry type.'));
//                Mage::logException($e);
            }
            /*
            $customer = Mage::getModel('customer/customer')
                ->setId($this->_getSession()->getCustomerId())
                ->setWebsiteId($this->_getSession()->getCustomer()->getWebsiteId());

            $fields = Mage::getConfig()->getFieldset('customer_account');
            $data = $this->_filterPostData($this->getRequest()->getPost());

            foreach ($fields as $code=>$node) {
                if ($node->is('update') && isset($data[$code])) {
                    $customer->setData($code, $data[$code]);
                }
            }

            $errors = $customer->validate();
            if (!is_array($errors)) {
                $errors = array();
            }
*/
            if ($isError) {
                $this->_getSession()->setGiftRegistryEntityFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;

                // set current data to sess
                // redirect to edit
            } else {
                $this->_redirect('*/*/');

                // redirect to next step
            }
        }
        Mage::log('Index ENDING ');
        $this->_redirect('*/*/');
    }

    protected function _initEntity($requestParam = 'id')
    {
        $entity = Mage::getModel('enterprise_giftregistry/entity');

        if ($entityId = $this->getRequest()->getParam($requestParam)) {
            $entity->load($entityId);
            if (!$entity->getId()) {
                Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Gift registry is not longer exists.'));
            }
        }

        return $entity;
    }
}
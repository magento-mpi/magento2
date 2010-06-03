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
        $count = 0;

        try {
            $entity = $this->_getActiveEntity();
            if ($entity && $entity->getId()) {
                $quote = Mage::getSingleton('checkout/cart')->getQuote();
                foreach ($quote->getAllVisibleItems() as $item) {
                    $entity->addItem($item);
                    $count += $item->getQty();
                }
                if ($count > 0) {
                    Mage::getSingleton('checkout/session')->addSuccess(
                        Mage::helper('enterprise_giftregistry')->__('%d shopping cart item(s) have been added to gift registry.', $count)
                    );
                } else {
                    Mage::getSingleton('checkout/session')->addNotice(
                        Mage::helper('enterprise_giftregistry')->__('Nothing to add to gift registry.')
                    );
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('giftregistry');
            return;
        }
        catch (Exception $e) {
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
            try {
                $entity = $this->_getActiveEntity();
                if ($entity && $entity->getId() && is_array($items)) {
                    foreach (array_keys($items) as $item) {
                        $entity->addItem((int)$item);
                    }
                    if (count($items) > 0) {
                        $this->_getSession()->addSuccess(
                            Mage::helper('enterprise_giftregistry')->__('%d wishlist item(s) have been added to gift registry.', count($items))
                        );
                    } else {
                        $this->_getSession()->addNotice(
                            Mage::helper('enterprise_giftregistry')->__('Nothing to add to gift registry.')
                        );
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('giftregistry');
                return;
            }
            catch (Exception $e) {
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
                        if ($this->_initEntity()->sendShareRegistryEmail($recipient, null, $senderMessage, $sender)) {
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
        $this->_initLayoutMessages('customer/session');
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
        $entityId = $this->getRequest()->getParam('entity_id');
        try {
            if (!$typeId) {
                if (!$entityId) {
                    $this->_redirect('*/*/addselect');
                    return;
                } else {
                    // editing existing entity
                    /* @var $model Enterprise_GiftRegistry_Model_Entity */
                    $model = Mage::getModel('enterprise_giftregistry/entity')->load($entityId);
                    if (!$model->getId()) {
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Gift registry is not longer exists.'));
                    }
                }
            }

            if ($typeId && !$entityId) {
                // creating new entity
                /* @var $model Enterprise_GiftRegistry_Model_Entity */
                $model = Mage::getSingleton('enterprise_giftregistry/entity');
                if ($model->setTypeById($typeId) === false) {
                    Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Incorrect Type.'));
                }
            }

            Mage::register('enterprise_giftregistry_entity', $model);
            Mage::register('enterprise_giftregistry_address', $model->exportAddress());

            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/addselect');
        }
    }

    /**
     * Create Registry action
     */
    public function editPostAction()
    {
        if (!($typeId = $this->getRequest()->getParam('type_id'))) {
            $this->_redirect('*/*/addselect');
            return;
        }

        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/edit', array('type_id', $typeId));
            return ;
        }

        if ($this->getRequest()->isPost() && ($data = $this->getRequest()->getPost())) {
            $entityId = $this->getRequest()->getParam('entity_id');
            $isError = false;
            $isAddAction = true;
            try {
                $model = Mage::getModel('enterprise_giftregistry/entity');
                if ($entityId){
                    $isAddAction = false;
                    $model->load($entityId);
                    if (!$model->getId()){
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Incorrect Registry Entity.'));
                    }
                }
                if ($isAddAction) {
                    $entityId = null;
                    if ($model->setTypeById($typeId) === false) {
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Incorrect Type.'));
                    }
                }

                $data = $this->_filterDates($data, array('event_date'));
                $model->importData($data, $isAddAction);

                $registrantsPost = $this->getRequest()->getParam('registrant');
                $persons = array();
                if (is_array($registrantsPost)) {
                    foreach  ($registrantsPost as $index => $registrant) {
                        if (is_array($registrant)) {
                            /* @var $person Enterprise_GiftRegistry_Model_Person */
                            $person = Mage::getModel('enterprise_giftregistry/person');
                            $idField = $person->getIdFieldName();
                            if (!empty($registrant[$idField])) {
                                $person->load($registrant[$idField]);
                                if (!$person->getId()) {
                                    Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Incorrect registrant data.'));
                                }
                            } else {
                                unset($registrant['person_id']);
                            }
                            $person->setData($registrant);
                            $errors = $person->validate();
                            if ($errors !== true) {
                                foreach ($errors as $err) {
                                    $this->_getSession()->addError($err);
                                }
                                $isError = true;
                            } else {
                                $persons[] = $person;
                            }
                        }
                    }
                }
                $addressTypeOrId = $this->getRequest()->getParam('address_type_or_id');
                if (!$addressTypeOrId || $addressTypeOrId == Enterprise_GiftRegistry_Helper_Data::ADDRESS_NEW) {
                    // creating new address
                    if (!empty($data['address'])) {
                        /* @var $address Mage_Customer_Model_Address */
                        $address = Mage::getModel('customer/address');
                        $address->setData($data['address']);
                        $errors = $address->validate();
                        $model->importAddress($address);
                    } else {
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Address is empty.'));
                    }
                    if ($errors !== true) {
                        foreach ($errors as $err) {
                            $this->_getSession()->addError($err);
                        }
                        $isError = true;
                    }
                } else if ($addressTypeOrId != Enterprise_GiftRegistry_Helper_Data::ADDRESS_NONE) {
                    // using one of existing Customer adressess
                    $addressId = $addressTypeOrId;
                    if (!$addressId) {
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('No address selected.'));
                    }
                    /* @var $customer Mage_Customer_Model_Customer */
                    $customer  = Mage::getSingleton('customer/session')->getCustomer();

                    $address = $customer->getAddressItemById($addressId);
                    if (!$address) {
                        Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Incorrect address selected.'));
                    }
                    $model->importAddress($address);
                }

                if (!$isError) {
                    $model->save();
                    $entityId = $model->getId();
                    $personLeft = array();
                    foreach ($persons as $person) {
                        $person->setEntityId($entityId);
                        $person->save();
                        $personLeft[] = $person->getId();
                    }
                    if (!$isAddAction) {
                        Mage::getModel('enterprise_giftregistry/person')
                            ->getResource()
                            ->deleteOrphan($entityId, $personLeft);
                    }
                    $this->_getSession()->addSuccess(
                        Mage::helper('enterprise_giftregistry/data')->__('Data saved succesfully.')
                    );
                    if ($isAddAction) {
                        $model->sendNewRegistryEmail();
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $isError = true;
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addError($this->__('Failed to save gift registry.'));
                Mage::logException($e);
                $isError = true;
            }

            if ($isError) {
                $this->_getSession()->setGiftRegistryEntityFormData($this->getRequest()->getPost());
                $params = $isAddAction ? array('type_id' => $typeId) : array('entity_id' => $entityId);
                $this->_redirect('*/*/edit', $params);
                return $this;
            } else {
                $this->_redirect('*/*/');
            }
        }
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

<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry frontend controller
 */
class Enterprise_GiftRegistry_Controller_Index extends Magento_Core_Controller_Front_Action
{
    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @return Enterprise_GiftRegistry_Controller_Index
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Enterprise_GiftRegistry_Helper_Data')->isEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->getResponse()->setRedirect($this->_objectManager->get('Magento_Customer_Helper_Data')->getLoginUrl());
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * View gift registry list in 'My Account' section
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        if ($block = $this->getLayout()->getBlock('giftregistry_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Registry'));
        }
        $this->renderLayout();
    }

    /**
     * Add quote items to customer active gift registry
     *
     * @return void
     */
    public function cartAction()
    {
        $count = 0;
        try {
            $entity = $this->_initEntity('entity');
            if ($entity && $entity->getId()) {
                $skippedItems = 0;
                $request = $this->getRequest();
                if ($request->getParam('product')) {//Adding from product page
                    $entity->addItem($request->getParam('product'), new Magento_Object($request->getParams()));
                    $count = ($request->getParam('qty')) ? $request->getParam('qty') : 1;
                } else {//Adding from cart
                    $cart = Mage::getSingleton('Magento_Checkout_Model_Cart');
                    foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
                        if (!$this->_objectManager->get('Enterprise_GiftRegistry_Helper_Data')->canAddToGiftRegistry($item)) {
                            $skippedItems++;
                            continue;
                        }
                        $entity->addItem($item);
                        $count += $item->getQty();
                        $cart->removeItem($item->getId());
                    }
                    $cart->save();
                }

                if ($count > 0) {
                    Mage::getSingleton('Magento_Checkout_Model_Session')->addSuccess(
                        __('%1 item(s) have been added to the gift registry.', $count)
                    );
                } else {
                    Mage::getSingleton('Magento_Checkout_Model_Session')->addNotice(
                        __('We have nothing to add to this gift registry.')
                    );
                }
                if (!empty($skippedItems)) {
                    Mage::getSingleton('Magento_Checkout_Model_Session')->addNotice(
                        __("You can't add virtual products, digital products or gift cards to gift registries.")
                    );
                }
            }
        } catch (Magento_Core_Exception $e) {
            if ($e->getCode() == Enterprise_GiftRegistry_Model_Entity::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $this->_getCheckoutSession()->addError($e->getMessage());
                $this->_redirectReferer('*/*');
            } else {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('giftregistry');
            }
            return;
        } catch (Exception $e) {
            Mage::getSingleton('Magento_Checkout_Model_Session')->addError(__('Failed to add shopping cart items to gift registry.'));
        }

        if ($entity->getId()) {
            $this->_redirect('giftregistry/index/items', array('id' => $entity->getId()));
        } else {
            $this->_redirect('giftregistry');
        }
    }

    /**
     * Add wishlist items to customer active gift registry action
     *
     * @return void
     */
    public function wishlistAction()
    {
        $itemId = $this->getRequest()->getParam('item');
        $redirectParams = array();
        if ($itemId) {
            try {
                $entity = $this->_initEntity('entity');
                $wishlistItem = Mage::getModel('Magento_Wishlist_Model_Item')
                    ->loadWithOptions($itemId, 'info_buyRequest');
                $entity->addItem($wishlistItem->getProductId(), $wishlistItem->getBuyRequest());
                $this->_getSession()->addSuccess(
                    __('The wish list item has been added to this gift registry.')
                );
                $redirectParams['wishlist_id'] = $wishlistItem->getWishlistId();
            } catch (Magento_Core_Exception $e) {
                if ($e->getCode() == Enterprise_GiftRegistry_Model_Entity::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $product = Mage::getModel('Magento_Catalog_Model_Product')->load((int)$wishlistItem->getProductId());
                    $query['options'] = Enterprise_GiftRegistry_Block_Product_View::FLAG;
                    $query['entity'] = $this->getRequest()->getParam('entity');
                    $this->_redirectUrl($product->getUrlModel()->getUrl($product, array('_query' => $query)));
                    return;
                }
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('giftregistry');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError(__("We couldn’t add your wish list items to your gift registry."));
            }
        }

        $this->_redirect('wishlist', $redirectParams);
    }

    /**
     * Delete selected gift registry entity
     *
     * @return void
     */
    public function deleteAction()
    {
        try {
            $entity = $this->_initEntity();
            if ($entity->getId()) {
                $entity->delete();
                $this->_getSession()->addSuccess(
                    __('You deleted this gift registry.')
                );
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $message = __('Something went wrong while deleting the gift registry.');
            $this->_getSession()->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Share selected gift registry entity
     *
     * @return void
     */
    public function shareAction()
    {
        try {
            $entity = $this->_initEntity();
            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Customer_Model_Session');
            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {
                $headBlock->setTitle(__('Share Gift Registry'));
            }
            $this->getLayout()->getBlock('giftregistry.customer.share')->setEntity($entity);
            $this->renderLayout();
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $message = __('Something went wrong while sharing the gift registry.');
            $this->_getSession()->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }

    /**
     * View items of selected gift registry entity
     *
     * @return void
     */
    public function itemsAction()
    {
        try {
            Mage::register('current_entity', $this->_initEntity());
            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Customer_Model_Session');
            $this->_initLayoutMessages('Magento_Checkout_Model_Session');
            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {
                $headBlock->setTitle(__('Gift Registry Items'));
            }
            $this->renderLayout();
            return;
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Update gift registry items
     *
     * @return void
     */
    public function updateItemsAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        try {
            $entity = $this->_initEntity();
            if ($entity->getId()) {
                $items = $this->getRequest()->getParam('items');
                $entity->updateItems($items);
                $this->_getSession()->addSuccess(
                    __('You updated the gift registry items.')
                );
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (Magento_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__("We couldn't update the gift registry."));
        }
        $this->_redirect('*/*/items', array('_current' => true));
    }

    /**
     * Share selected gift registry entity
     *
     * @return void
     */
    public function sendAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/share', array('_current' => true));
            return;
        }

        try {
            /** @var $entity Enterprise_GiftRegistry_Model_Entity */
            $entity = $this->_initEntity()->setData($this->getRequest()->getPost());

            $result = $entity->sendShareRegistryEmails();

            if ($result->getIsSuccess()) {
                $this->_getSession()->addSuccess($result->getSuccessMessage());
            } else {
                $this->_getSession()->addError($result->getErrorMessage());
                $this->_getSession()->setSharingForm($this->getRequest()->getPost());
                $this->_redirect('*/*/share', array('_current' => true));
                return;
            }
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $message = __('Something went wrong while sending email(s).');
            $this->_getSession()->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Get current customer session
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Get current checkout session
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }

    /**
     * Add select gift registry action
     *
     * @return void
     */
    public function addSelectAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        if ($block = $this->getLayout()->getBlock('giftregistry_addselect')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Create Gift Registry'));
        }
        $this->renderLayout();
    }

    /**
     * Select gift registry type action
     *
     * @return void
     */
    public function editAction()
    {
        $typeId = $this->getRequest()->getParam('type_id');
        $entityId = $this->getRequest()->getParam('entity_id');
        try {
            if (!$typeId) {
                if (!$entityId) {
                    $this->_redirect('*/*/');
                    return;
                } else {
                    // editing existing entity
                    /* @var $model Enterprise_GiftRegistry_Model_Entity */
                    $model = $this->_initEntity('entity_id');
                }
            }

            if ($typeId && !$entityId) {
                // creating new entity
                /* @var $model Enterprise_GiftRegistry_Model_Entity */
                $model = Mage::getSingleton('Enterprise_GiftRegistry_Model_Entity');
                if ($model->setTypeById($typeId) === false) {
                    Mage::throwException(__('Please correct the gift registry.'));
                }
            }

            Mage::register('enterprise_giftregistry_entity', $model);
            Mage::register('enterprise_giftregistry_address', $model->exportAddress());

            $this->loadLayout();
            $this->_initLayoutMessages('Magento_Customer_Model_Session');

            if ($model->getId()) {
                $pageTitle = __('Edit Gift Registry');
            } else {
                $pageTitle = __('Create Gift Registry');
            }
            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {
                $headBlock->setTitle($pageTitle);
            }
            $this->renderLayout();
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }

    /**
     * Create gift registry action
     *
     * @return void
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
                if ($entityId){
                    $isAddAction = false;
                    $model = $this->_initEntity('entity_id');
                }
                if ($isAddAction) {
                    $entityId = null;
                    $model = Mage::getModel('Enterprise_GiftRegistry_Model_Entity');
                    if ($model->setTypeById($typeId) === false) {
                        Mage::throwException(__('Incorrect Type'));
                    }
                }

                $data = $this->_objectManager->get('Enterprise_GiftRegistry_Helper_Data')->filterDatesByFormat(
                    $data,
                    $model->getDateFieldArray()
                );
                $data = $this->_filterPost($data);
                $this->getRequest()->setPost($data);
                $model->importData($data, $isAddAction);

                $registrantsPost = $this->getRequest()->getPost('registrant');
                $persons = array();
                if (is_array($registrantsPost)) {
                    foreach  ($registrantsPost as $index => $registrant) {
                        if (is_array($registrant)) {
                            /* @var $person Enterprise_GiftRegistry_Model_Person */
                            $person = Mage::getModel('Enterprise_GiftRegistry_Model_Person');
                            $idField = $person->getIdFieldName();
                            if (!empty($registrant[$idField])) {
                                $person->load($registrant[$idField]);
                                if (!$person->getId()) {
                                    Mage::throwException(__('Please correct the recipient data.'));
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
                        /* @var $address Magento_Customer_Model_Address */
                        $address = Mage::getModel('Magento_Customer_Model_Address');
                        $address->setData($data['address']);
                        $errors = $address->validate();
                        $model->importAddress($address);
                    } else {
                        Mage::throwException(__('Address is empty.'));
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
                        Mage::throwException(__('Please select an address.'));
                    }
                    /* @var $customer Magento_Customer_Model_Customer */
                    $customer  = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();

                    $address = $customer->getAddressItemById($addressId);
                    if (!$address) {
                        Mage::throwException(__('Please correct the address.'));
                    }
                    $model->importAddress($address);
                }
                $errors = $model->validate();
                if ($errors !== true) {
                    foreach ($errors as $err) {
                        $this->_getSession()->addError($err);
                    }
                    $isError = true;
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
                        Mage::getModel('Enterprise_GiftRegistry_Model_Person')
                            ->getResource()
                            ->deleteOrphan($entityId, $personLeft);
                    }
                    $this->_getSession()->addSuccess(
                        __('You saved this gift registry.')
                    );
                    if ($isAddAction) {
                        $model->sendNewRegistryEmail();
                    }
                }
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $isError = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    __("We couldn't save this gift registry.")
                );
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

    /**
     * Load gift registry entity model by request argument
     *
     * @param string $requestParam
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    protected function _initEntity($requestParam = 'id')
    {
        $entity = Mage::getModel('Enterprise_GiftRegistry_Model_Entity');
        $customerId = $this->_getSession()->getCustomerId();
        $entityId = $this->getRequest()->getParam($requestParam);

        if ($entityId) {
            $entity->load($entityId);
            if (!$entity->getId() || $entity->getCustomerId() != $customerId) {
                Mage::throwException(__('Please correct the gift registry ID.'));
            }
        }
        return $entity;
    }

     /**
     * Strip tags from received data
     *
     * @param  string|array $data
     * @return mixed
     */
    protected function _filterPost($data)
    {
        if (!is_array($data)) {
            return strip_tags($data);
        }
        foreach ($data as &$field) {
            if (!empty($field)) {
                if (!is_array($field)) {
                    $field = strip_tags($field);
                } else {
                    $field = $this->_filterPost($field);
                }
            }
        }
        return $data;
    }
}

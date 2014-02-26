<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller;

use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;
use Magento\App\ResponseInterface;
use Magento\Core\Exception;

/**
 * Gift registry frontend controller
 */
class Index extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Core\App\Action\FormKeyValidator
     */
    protected $_formKeyValidator;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Only logged in users can use this functionality,
     * this function checks if user is logged in before all other actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->isEnabled()) {
            throw new NotFoundException();
        }

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->getResponse()->setRedirect(
                $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl()
            );
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * View gift registry list in 'My Account' section
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $block = $this->_view->getLayout()->getBlock('giftregistry_list');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Gift Registry'));
        }
        $this->_view->renderLayout();
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
                    $entity->addItem($request->getParam('product'), new \Magento\Object($request->getParams()));
                    $count = ($request->getParam('qty')) ? $request->getParam('qty') : 1;
                } else {//Adding from cart
                    $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
                    foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
                        if (!$this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->canAddToGiftRegistry($item)) {
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
                    $this->messageManager->addSuccess(
                        __('%1 item(s) have been added to the gift registry.', $count)
                    );
                } else {
                    $this->messageManager->addNotice(
                        __('We have nothing to add to this gift registry.')
                    );
                }
                if (!empty($skippedItems)) {
                    $this->messageManager->addNotice(
                        __("You can't add virtual products, digital products or gift cards to gift registries.")
                    );
                }
            }
        } catch (Exception $e) {
            if ($e->getCode() == \Magento\GiftRegistry\Model\Entity::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl('*/*'));
            } else {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('giftregistry');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Failed to add shopping cart items to gift registry.'));
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
                $wishlistItem = $this->_objectManager->create('Magento\Wishlist\Model\Item')
                    ->loadWithOptions($itemId, 'info_buyRequest');
                $entity->addItem($wishlistItem->getProductId(), $wishlistItem->getBuyRequest());
                $this->messageManager->addSuccess(
                    __('The wish list item has been added to this gift registry.')
                );
                $redirectParams['wishlist_id'] = $wishlistItem->getWishlistId();
            } catch (Exception $e) {
                if ($e->getCode() == \Magento\GiftRegistry\Model\Entity::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                        ->load((int)$wishlistItem->getProductId());
                    $query['options'] = \Magento\GiftRegistry\Block\Product\View::FLAG;
                    $query['entity'] = $this->getRequest()->getParam('entity');
                    $this->getResponse()->setRedirect($product->getUrlModel()->getUrl($product, array('_query' => $query)));
                    return;
                }
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('giftregistry');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn’t add your wish list items to your gift registry."));
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
                $this->messageManager->addSuccess(
                    __('You deleted this gift registry.')
                );
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while deleting the gift registry.');
            $this->messageManager->addException($e, $message);
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
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $headBlock = $this->_view->getLayout()->getBlock('head');
            if ($headBlock) {
                $headBlock->setTitle(__('Share Gift Registry'));
            }
            $this->_view->getLayout()->getBlock('giftregistry.customer.share')->setEntity($entity);
            $this->_view->renderLayout();
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while sharing the gift registry.');
            $this->messageManager->addException($e, $message);
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
            $this->_coreRegistry->register('current_entity', $this->_initEntity());
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $headBlock = $this->_view->getLayout()->getBlock('head');
            if ($headBlock) {
                $headBlock->setTitle(__('Gift Registry Items'));
            }
            $this->_view->renderLayout();
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Update gift registry items
     *
     * @return void|ResponseInterface
     */
    public function updateItemsAction()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('*/*/');
        }

        try {
            $entity = $this->_initEntity();
            if ($entity->getId()) {
                $items = $this->getRequest()->getParam('items');
                $entity->updateItems($items);
                $this->messageManager->addSuccess(
                    __('You updated the gift registry items.')
                );
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        } catch (\Magento\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We couldn't update the gift registry."));
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
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/share', array('_current' => true));
            return;
        }

        try {
            /** @var $entity \Magento\GiftRegistry\Model\Entity */
            $entity = $this->_initEntity()->addData($this->getRequest()->getPost());

            $result = $entity->sendShareRegistryEmails();

            if ($result->getIsSuccess()) {
                $this->messageManager->addSuccess($result->getSuccessMessage());
            } else {
                $this->messageManager->addError($result->getErrorMessage());
                $this->_getSession()->setSharingForm($this->getRequest()->getPost());
                $this->_redirect('*/*/share', array('_current' => true));
                return;
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while sending email(s).');
            $this->messageManager->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Get current customer session
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }

    /**
     * Get current checkout session
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }

    /**
     * Add select gift registry action
     *
     * @return void
     */
    public function addSelectAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $block = $this->_view->getLayout()->getBlock('giftregistry_addselect');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Create Gift Registry'));
        }
        $this->_view->renderLayout();
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
                    /* @var $model \Magento\GiftRegistry\Model\Entity */
                    $model = $this->_initEntity('entity_id');
                }
            }

            if ($typeId && !$entityId) {
                // creating new entity
                /* @var $model \Magento\GiftRegistry\Model\Entity */
                $model = $this->_objectManager->get('Magento\GiftRegistry\Model\Entity');
                if ($model->setTypeById($typeId) === false) {
                    throw new Exception(__('Please correct the gift registry.'));
                }
            }

            $this->_coreRegistry->register('magento_giftregistry_entity', $model);
            $this->_coreRegistry->register('magento_giftregistry_address', $model->exportAddress());

            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();

            if ($model->getId()) {
                $pageTitle = __('Edit Gift Registry');
            } else {
                $pageTitle = __('Create Gift Registry');
            }
            $headBlock = $this->_view->getLayout()->getBlock('head');
            if ($headBlock) {
                $headBlock->setTitle($pageTitle);
            }
            $this->_view->renderLayout();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }

    /**
     * Create gift registry action
     *
     * @return void|ResponseInterface
     */
    public function editPostAction()
    {
        if (!($typeId = $this->getRequest()->getParam('type_id'))) {
            $this->_redirect('*/*/addselect');
            return;
        }

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/edit', array('type_id', $typeId));
            return;
        }

        if ($this->getRequest()->isPost() && ($data = $this->getRequest()->getPost())) {
            $entityId = $this->getRequest()->getParam('entity_id');
            $isError = false;
            $isAddAction = true;
            try {
                if ($entityId) {
                    $isAddAction = false;
                    $model = $this->_initEntity('entity_id');
                }
                if ($isAddAction) {
                    $entityId = null;
                    $model = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
                    if ($model->setTypeById($typeId) === false) {
                        throw new Exception(__('Incorrect Type'));
                    }
                }

                $data = $this->_objectManager->get('Magento\GiftRegistry\Helper\Data')->filterDatesByFormat(
                    $data,
                    $model->getDateFieldArray()
                );
                $data = $this->_filterPost($data);
                $this->getRequest()->setPost($data);
                $model->importData($data, $isAddAction);

                $registrantsPost = $this->getRequest()->getPost('registrant');
                $persons = array();
                if (is_array($registrantsPost)) {
                    foreach ($registrantsPost as $registrant) {
                        if (is_array($registrant)) {
                            /* @var $person \Magento\GiftRegistry\Model\Person */
                            $person = $this->_objectManager->create('Magento\GiftRegistry\Model\Person');
                            $idField = $person->getIdFieldName();
                            if (!empty($registrant[$idField])) {
                                $person->load($registrant[$idField]);
                                if (!$person->getId()) {
                                    throw new Exception(__('Please correct the recipient data.'));
                                }
                            } else {
                                unset($registrant['person_id']);
                            }
                            $person->setData($registrant);
                            $errors = $person->validate();
                            if ($errors !== true) {
                                foreach ($errors as $err) {
                                    $this->messageManager->addError($err);
                                }
                                $isError = true;
                            } else {
                                $persons[] = $person;
                            }
                        }
                    }
                }
                $addressTypeOrId = $this->getRequest()->getParam('address_type_or_id');
                if (!$addressTypeOrId || $addressTypeOrId == \Magento\GiftRegistry\Helper\Data::ADDRESS_NEW) {
                    // creating new address
                    if (!empty($data['address'])) {
                        /* @var $address \Magento\Customer\Model\Address */
                        $address = $this->_objectManager->create('Magento\Customer\Model\Address');
                        $address->setData($data['address']);
                        $errors = $address->validate();
                        $model->importAddress($address);
                    } else {
                        throw new Exception(__('Address is empty.'));
                    }
                    if ($errors !== true) {
                        foreach ($errors as $err) {
                            $this->messageManager->addError($err);
                        }
                        $isError = true;
                    }
                } else if ($addressTypeOrId != \Magento\GiftRegistry\Helper\Data::ADDRESS_NONE) {
                    // using one of existing Customer addresses
                    $addressId = $addressTypeOrId;
                    if (!$addressId) {
                        throw new Exception(__('Please select an address.'));
                    }
                    /* @var $customer \Magento\Customer\Model\Customer */
                    $customer  = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer();

                    $address = $customer->getAddressItemById($addressId);
                    if (!$address) {
                        throw new Exception(__('Please correct the address.'));
                    }
                    $model->importAddress($address);
                }
                $errors = $model->validate();
                if ($errors !== true) {
                    foreach ($errors as $err) {
                        $this->messageManager->addError($err);
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
                        $this->_objectManager->create('Magento\GiftRegistry\Model\Person')
                            ->getResource()
                            ->deleteOrphan($entityId, $personLeft);
                    }
                    $this->messageManager->addSuccess(
                        __('You saved this gift registry.')
                    );
                    if ($isAddAction) {
                        $model->sendNewRegistryEmail();
                    }
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $isError = true;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __("We couldn't save this gift registry.")
                );
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $isError = true;
            }

            if ($isError) {
                $this->_getSession()->setGiftRegistryEntityFormData($this->getRequest()->getPost());
                $params = $isAddAction ? array('type_id' => $typeId) : array('entity_id' => $entityId);
                return $this->_redirect('*/*/edit', $params);
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
     * @return \Magento\GiftRegistry\Model\Entity
     * @throws Exception
     */
    protected function _initEntity($requestParam = 'id')
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $customerId = $this->_getSession()->getCustomerId();
        $entityId = $this->getRequest()->getParam($requestParam);

        if ($entityId) {
            $entity->load($entityId);
            if (!$entity->getId() || $entity->getCustomerId() != $customerId) {
                throw new Exception(__('Please correct the gift registry ID.'));
            }
        }
        return $entity;
    }

    /**
     * Strip tags from received data
     *
     * @param string|array $data
     * @return string|array
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

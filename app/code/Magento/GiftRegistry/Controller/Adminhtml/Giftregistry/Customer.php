<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Adminhtml\Giftregistry;

use Magento\Core\Exception;
use Magento\GiftRegistry\Model\Entity;

/**
 * Gift Registry controller
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Customer extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\App\Action\Title
     */
    protected $_title;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @param string $requestParam
     * @return Entity
     * @throws Exception
     */
    protected function _initEntity($requestParam = 'id')
    {
        $entity = $this->_objectManager->create('Magento\GiftRegistry\Model\Entity');
        $entityId = $this->getRequest()->getParam($requestParam);
        if ($entityId) {
            $entity->load($entityId);
            if (!$entity->getId()) {
                throw new Exception(__('Please correct the gift registry entity.'));
            }
        }
        $this->_coreRegistry->register('current_giftregistry_entity', $entity);
        return $entity;
    }

    /**
     * Get customer gift registry grid
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Get customer gift registry info block
     *
     * @return void
     */
    public function editAction()
    {
        try {
            $model = $this->_initEntity();
            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($model->getCustomerId());

            $this->_title->add(__('Customers'));
            $this->_title->add(__('Customers'));
            $this->_title->add($customer->getName());
            $this->_title->add(__("Edit '%1' Gift Registry", $model->getTitle()));

            $this->_view->loadLayout()->renderLayout();
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customer/index/edit', array(
                'id'         => $this->getRequest()->getParam('customer'),
                'active_tab' => 'giftregistry'
            ));
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong while editing the gift registry.'));
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->_redirect('customer/index/edit', array(
                'id'         => $this->getRequest()->getParam('customer'),
                'active_tab' => 'giftregistry'
            ));
        }
    }

    /**
     * Add quote items to gift registry
     *
     * @return void
     */
    public function addAction()
    {
        if ($quoteIds = $this->getRequest()->getParam('products')) {
            $model = $this->_initEntity();
            try {
                $skippedItems = $model->addQuoteItems($quoteIds);
                if (count($quoteIds) - $skippedItems > 0) {
                    $this->messageManager->addSuccess(__('Shopping cart items have been added to gift registry.'));
                }
                if ($skippedItems) {
                    $this->messageManager->addNotice(
                        __('Virtual, Downloadable, and virtual Gift Card products cannot be added to gift registries.')
                    );
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Failed to add shopping cart items to gift registry.'));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
    }

    /**
     * Update gift registry items qty
     *
     * @return void
     */
    public function updateAction()
    {
        $items = $this->getRequest()->getParam('items');
        $entity = $this->_initEntity();
        $updatedCount = 0;

        if (is_array($items)) {
            try {
                $model = $this->_objectManager->create('Magento\GiftRegistry\Model\Item');
                foreach ($items as $itemId => $data) {
                    if (!empty($data['action'])) {
                        $model->load($itemId);
                        if ($model->getId() && $model->getEntityId() == $entity->getId()) {
                            if ($data['action'] == 'remove') {
                                $model->delete();
                            } else {
                                $model->setQty($data['qty']);
                                $model->save();
                            }
                        }
                        $updatedCount++;
                    }
                }
                if ($updatedCount) {
                    $this->messageManager->addSuccess(__('You updated this gift registry.'));
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('id' => $entity->getId()));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't update these gift registry items."));
                $this->_objectManager->get('Magento\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $entity->getId()));
    }

    /**
     * Share gift registry action
     *
     * @return void
     */
    public function shareAction()
    {
        $model = $this->_initEntity();
        $data = $this->getRequest()->getParam('emails');
        if ($data) {
            $emails = explode(',', $data);
            $emailsForSend = array();

            if ($this->_storeManager->hasSingleStore()) {
                $storeId = $this->_storeManager->getStore(true)->getId();
            } else {
                $storeId = $this->getRequest()->getParam('store_id');
            }
            $model->setStoreId($storeId);

            try {
                $sentCount   = 0;
                $failedCount = 0;
                foreach ($emails as $email) {
                    if (!empty($email)) {
                        if ($model->sendShareRegistryEmail(
                            $email,
                            $storeId,
                            $this->getRequest()->getParam('message')
                        )
                        ) {
                            $sentCount++;
                        } else {
                            $failedCount++;
                        }
                        $emailsForSend[] = $email;
                    }
                }
                if (empty($emailsForSend)) {
                    throw new Exception(__('Please enter at least one email address.'));
                }
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            if ($sentCount) {
                $this->messageManager->addSuccess(__('%1 email(s) were sent.', $sentCount));
            }
            if ($failedCount) {
                $this->messageManager->addError(
                    __("We couldn't send '%1 of %2 emails.", $failedCount, count($emailsForSend))
                );
            }
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
    }

    /**
     * Delete gift registry action
     *
     * @return void
     */
    public function deleteAction()
    {
        try {
            $model = $this->_initEntity();
            $customerId = $model->getCustomerId();
            $model->delete();
            $this->messageManager->addSuccess(__('You deleted this gift registry entity.'));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/edit', array('id' => $model->getId()));
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__("We couldn't delete this gift registry entity."));
            $this->_objectManager->get('Magento\Logger')->logException($e);
        }
        $this->_redirect('customer/index/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_GiftRegistry::customer_magento_giftregistry');
    }
}

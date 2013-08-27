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
 * Product tags admin controller
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Controller_Adminhtml_Tag extends Magento_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(
                __('Catalog'), __('Catalog')
            )
            ->_addBreadcrumb(
                __('Tags'), __('Tags')
            );

        return $this;
    }

    /**
     * Prepare tag model for manipulation
     *
     * @return Magento_Tag_Model_Tag | false
     */
    protected function _initTag()
    {
        $model = Mage::getModel('Magento_Tag_Model_Tag');
        $storeId = $this->getRequest()->getParam('store');
        $model->setStoreId($storeId);

        if (($id = $this->getRequest()->getParam('tag_id'))) {
            $model->setAddBasePopularity();
            $model->load($id);
            $model->setStoreId($storeId);

            if (!$model->getId()) {
                return false;
            }
        }

        Mage::register('current_tag', $model);
        return $model;
    }

    /**
     * Show grid action
     *
     */
    public function indexAction()
    {
        $this->_title(__('All Tags'));

        $this->_initAction()
            ->_addBreadcrumb(
                __('All Tags'),
                __('All Tags')
            )
            ->_setActiveMenu('Magento_Tag::catalog_tag_all')
            ->renderLayout();
    }

    /**
     * Action to draw grid loaded by ajax
     *
     */
    public function ajaxGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action to draw pending tags grid loaded by ajax
     *
     */
    public function ajaxPendingGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * New tag action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit tag action
     *
     */
    public function editAction()
    {
        $this->_title(__('Tags'));

        if (! (int) $this->getRequest()->getParam('store')) {
            return $this->_redirect(
                '*/*/*/',
                array('store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true)
            );
        }

        if (! ($model = $this->_initTag())) {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                __('Please correct the tag and try again.')
            );
            return $this->_redirect('*/*/index', array('store' => $this->getRequest()->getParam('store')));
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getTagData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        $this->_title($model->getId() ? __('Edit Tag \'%1\'', $model->getName()) : __('New Tag'));

        Mage::register('tag_tag', $model);

        $this->_initAction()->_setActiveMenu('Magento_Tag::catalog_tag_all')->renderLayout();
    }

    /**
     * Save tag action
     *
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($postData['tag_id'])) {
                $data['tag_id'] = $postData['tag_id'];
            }

            $data['name']               = trim($postData['tag_name']);
            $data['status']             = $postData['tag_status'];
            $data['base_popularity']    = (isset($postData['base_popularity'])) ? $postData['base_popularity'] : 0;
            $data['store']              = $postData['store_id'];

            if (!$model = $this->_initTag()) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('Please correct the tag and try again.')
                );
                return $this->_redirect('*/*/index', array('store' => $data['store']));
            }

            $model->addData($data);

            if (isset($postData['tag_assigned_products'])) {
                $productIds = Mage::helper('Magento_Adminhtml_Helper_Js')->decodeGridSerializedInput(
                    $postData['tag_assigned_products']
                );
                $tagRelationModel = Mage::getModel('Magento_Tag_Model_Tag_Relation');
                $tagRelationModel->addRelations($model, $productIds);
            }

            try {
                $model->save();

                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('You saved the tag.')
                );

                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setTagData(false);

                if (($continue = $this->getRequest()->getParam('continue'))) {
                    return $this->_redirect(
                        '*/tag/edit',
                        array('tag_id' => $model->getId(), 'store' => $model->getStoreId(), 'ret' => $continue)
                    );
                } else {
                    return $this->_redirect('*/tag/' . $this->getRequest()->getParam('ret', 'index'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setTagData($data);

                return $this->_redirect(
                    '*/*/edit',
                    array('tag_id' => $model->getId(), 'store' => $model->getStoreId())
                );
            }
        }

        return $this->_redirect('*/tag/index', array('_current' => true));
    }

    /**
     * Delete tag action
     *
     * @return void
     */
    public function deleteAction()
    {
        $model   = $this->_initTag();
        $session = Mage::getSingleton('Magento_Adminhtml_Model_Session');

        if ($model && $model->getId()) {
            try {
                $model->delete();
                $session->addSuccess(__('The tag has been deleted.'));
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        } else {
            $session->addError(__('We can\'t find a tag to delete.'));
        }

        $this->getResponse()->setRedirect($this->getUrl('*/tag/' . $this->getRequest()->getParam('ret', 'index')));
    }

    /**
     * Pending tags
     *
     */
    public function pendingAction()
    {
        $this->_title(__('Pending Tags'));

        $this->_initAction()
            ->_addBreadcrumb(
                __('Pending Tags'),
                __('Pending Tags')
            )
            ->renderLayout();
    }

    /**
     * Assigned products (with serializer block)
     *
     */
    public function assignedAction()
    {
        $this->_title(__('Assigned'));

        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Assigned products grid
     *
     */
    public function assignedGridOnlyAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Tagged products
     *
     */
    public function productAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customers
     *
     */
    public function customerAction()
    {
        $this->_initTag();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Massaction for removing tags
     *
     */
    public function massDeleteAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        if (!is_array($tagIds)) {
             Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('Magento_Tag_Model_Tag')->load($tagId);
                    $tag->delete();
                }
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }

    /**
     * Massaction for changing status of selected tags
     *
     */
    public function massStatusAction()
    {
        $tagIds = $this->getRequest()->getParam('tag');
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if (!is_array($tagIds)) {
            // No products selected
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('Please select tag(s).'));
        } else {
            try {
                foreach ($tagIds as $tagId) {
                    $tag = Mage::getModel('Magento_Tag_Model_Tag')
                        ->load($tagId)
                        ->setStatus($this->getRequest()->getParam('status'));
                     $tag->save();
                }
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('A total of %1 record(s) have been updated.', count($tagIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }
        $ret = $this->getRequest()->getParam('ret') ? $this->getRequest()->getParam('ret') : 'index';
        $this->_redirect('*/*/'.$ret);
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Tag::tag_all');
    }
}

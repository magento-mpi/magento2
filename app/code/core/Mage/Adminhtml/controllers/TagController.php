<?php
/**
 * Product tags admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('catalog/tag')
            ->_addBreadcrumb(__('Catalog'), __('Catalog'))
            ->_addBreadcrumb(__('Tags'), __('Tags'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'pending':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/pending');
                break;
            case 'all':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag/all');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('catalog/tag');
                break;
        }
    }
    
    public function indexAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('All Tags'), __('All Tags'))
            ->_setActiveMenu('catalog/tag/all')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_tag'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('tag_id');
        $model = Mage::getModel('tag/tag');

        if ($id) {
            $model->load($id);
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getTagData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('tag_tag', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit Tag') : __('New Tag'), $id ? __('Edit Tag') : __('New Tag'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_tag_edit')->setData('action', Mage::getUrl('*/tag_edit/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('tag/tag');
            $model->setData($data);

            switch( $this->getRequest()->getParam('ret') ) {
                case 'all':
                    $url = Mage::getUrl('*/*/', array(
                        'ret' => $this->getRequest()->getParam('ret'),
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                case 'pending':
                    $url = Mage::getUrl('*/tag/pending', array(
                        'ret' => $this->getRequest()->getParam('ret'),
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
                    break;

                default:
                    $url = Mage::getUrl('*/*/', array(
                        'ret' => $this->getRequest()->getParam('ret'),
                        'customer_id' => $this->getRequest()->getParam('customer_id'),
                        'product_id' => $this->getRequest()->getParam('product_id'),
                    ));
            }

            // $tag->setStoreId(Mage::getSingleton('core/store')->getId());
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Tag was saved succesfully'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);
                $this->getResponse()->setRedirect($url);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);
                $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
                return;
            }
        }
        $this->getResponse()->setRedirect($url);
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('tag_id')) {
            try {
                $model = Mage::getModel('tag/tag');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Tag was deleted succesfully'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(__('Unable to find a tag to delete'));
        $this->_redirect('*/*/');
    }

    /**
     * Pending tags
     *
     */
    public function pendingAction()
    {
        $this->_initAction()
            ->_addBreadcrumb(__('Pending Tags'), __('Pending Tags'))
            ->_setActiveMenu('catalog/tag/pending')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_pending'))
            ->renderLayout();
    }

    /**
     * Tagged products
     *
     */
    public function productAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));

        $this->_initAction()
            ->_addBreadcrumb(__('Products'), __('Products'))
            ->_setActiveMenu('catalog/tag/product')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_product'))
            ->renderLayout();
    }

    /**
     * Customers
     *
     */
    public function customerAction()
    {
        Mage::register('tagId', $this->getRequest()->getParam('tag_id'));

        $this->_initAction()
            ->_addBreadcrumb(__('Customers'), __('Customers'))
            ->_setActiveMenu('catalog/tag/customer')
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_customer'))
            ->renderLayout();
    }

}

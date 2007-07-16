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
class Mage_Adminhtml_TagController extends Mage_Adminhtml_Controller_Action {

    /**
     * Tags index action
     *
     */
    public function indexAction()
    {
        $this->_forward('all');
    }

    /**
     * Create/Edit tag form
     *
     */
    public function editAction()
    {
        $tagId = (int) $this->getRequest()->getParam('id');
        $tag = Mage::getModel('tag/tag');

        if ($tagId) {
            $tag->load($tagId);
        }

        Mage::register('tag', $tag);

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_edit'));

        if ($tagId) {
            $this->_addBreadcrumb(__('Tag').' '.$tag->getTagname(), __('tag').' '.$tag->getTagname());
        }
        else {
            $this->_addBreadcrumb(__('New Tag'), __('new tag title'));
        }

        $this->renderLayout();
    }

    /**
     * Create new tag action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save tag action
     *
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $tag = Mage::getModel('tag/tag')
                ->addData($data);

            if ($tagId = (int) $this->getRequest()->getParam('id')) {
                $tag->setId($tagId);
            }

            // TODO
            $tag->setStoreId(1);

            try {
                $tag->save();
            }
            catch (Exception $e){
                echo $e;
            }
        }

        $this->_redirect('adminhtml/tag/all');
    }

    /**
     * Delete tag action
     *
     */
    public function deleteAction()
    {
        if ($tagId = (int) $this->getRequest()->getParam('id')) {
            $tag = Mage::getModel('tag/tag');
            $tag->setId($tagId);

            // TODO
            $tag->setStoreId(1);

            try {
                $tag->delete();
            }
            catch (Exception $e){
                echo $e;
            }
        }

        $this->_redirect('adminhtml/tag/all');
    }

    /**
     * All tags grid
     *
     */
    public function allAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_all')->assign('header', __('Tags List')))
            ->renderLayout();
    }

    /**
     * Pending tags grid
     *
     */
    public function pendingAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_pending')->assign('header', __('Pending Tags')))
            ->_addBreadcrumb(__('Pending Tags'), __('products tags title'))
            ->renderLayout();
    }

    /**
     * Tagged products grid
     *
     */
    public function productsAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_products')->assign('header', __('Tagged Products')))
            ->_addBreadcrumb(__('Tagged Products'), __('products tags title'))
            ->renderLayout();
    }

    /**
     * Customers grid
     *
     */
    public function customersAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_customers')->assign('header', __('Customers')))
            ->_addBreadcrumb(__('Customers'), __('products tags title'))
            ->renderLayout();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_setActiveMenu('catalog_tags')
            ->_addBreadcrumb(__('Catalog'), __('catalog title'))
            ->_addBreadcrumb(__('Product Tags'), __('products tags title'))
            ->_addLeft(
                $this->getLayout()->createBlock('adminhtml/tag_tabs', 'tag_tabs')->setActiveTab(
                    $this->getRequest()->getActionName()
                )
            )
        ;
        return $this;
    }

}

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
            $this->_addBreadcrumb(__('Edit Tag').' '.$tag->getTagname(), __('Edit Tag').' '.$tag->getTagname());
        }
        else {
            $this->_addBreadcrumb(__('New Tag'), __('New Tag Title'));
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

//            print_r($data);

            $tag = Mage::getModel('tag/tag')
                ->setData($data);

            if ($tagId = (int) $this->getRequest()->getParam('id')) {
                $tag->setId($tagId);
            }

            $tag->setStoreId(Mage::getSingleton('core/store')->getId());

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

            // $tag->setStoreId(Mage::getSingleton('core/store')->getId());

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
            ->_addBreadcrumb(__('All Tags'), __('Products Tags Title'))
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
            ->_addBreadcrumb(__('Pending Tags'), __('Products Tags Title'))
            ->renderLayout();
    }

    /**
     * Tagged products grid
     *
     */
    public function productsAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tag_products'))
            ->_addBreadcrumb(__('Products'), __('Products Tags Title'))
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
            ->_addBreadcrumb(__('Customers'), __('Products Tags Title'))
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
            ->_setActiveMenu('catalog/tags')
            ->_addBreadcrumb(__('Catalog'), __('Catalog Title'))
            ->_addBreadcrumb(__('Tags'), __('Products Tags Title'))
//            ->_addLeft($this->getLayout()->createBlock('adminhtml/tag_tabs', 'tag_tabs')->setActiveTab($this->getRequest()->getActionName()))
        ;
        return $this;
    }

}

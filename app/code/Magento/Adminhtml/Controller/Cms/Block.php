<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms manage blocks controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Cms;

class Block extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Adminhtml\Controller\Cms\Block
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Magento_Cms::cms_block')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Static Blocks'), __('Static Blocks'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title(__('Blocks'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new CMS block
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS block
     */
    public function editAction()
    {
        $this->_title(__('Blocks'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('block_id');
        $model = \Mage::getModel('Magento\Cms\Model\Block');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : __('New Block'));

        // 3. Set entered data if was error when we do save
        $data = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('cms_block', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit Block') : __('New Block'), $id ? __('Edit Block') : __('New Block'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $id = $this->getRequest()->getParam('block_id');
            $model = \Mage::getModel('Magento\Cms\Model\Block')->load($id);
            if (!$model->getId() && $id) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('The block has been saved.'));
                // clear previously saved data from session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('block_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // save data in session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('block_id' => $this->getRequest()->getParam('block_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('block_id');
        if ($id) {
            try {
                // init model and delete
                $model = \Mage::getModel('Magento\Cms\Model\Block');
                $model->load($id);
                $model->delete();
                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(__('The block has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return;
            }
        }
        // display error message
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(__('We can\'t find a block to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::block');
    }
}

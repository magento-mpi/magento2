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
 * XML sitemap controller
 */
class Magento_Adminhtml_Controller_Sitemap extends  Magento_Adminhtml_Controller_Action
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
     * @return Magento_Adminhtml_Controller_Sitemap
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('Magento_Sitemap::catalog_sitemap')
            ->_addBreadcrumb(
                __('Catalog'),
                __('Catalog'))
            ->_addBreadcrumb(
                __('XML Sitemap'),
                __('XML Sitemap')
        );
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title(__('Site Map'));
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new sitemap
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit sitemap
     */
    public function editAction()
    {
        $this->_title(__('Site Map'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('sitemap_id');
        $model = Mage::getModel('Magento_Sitemap_Model_Sitemap');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('This sitemap no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getSitemapFilename() : __('New Site Map'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('sitemap_sitemap', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? __('Edit Sitemap') : __('New Sitemap'),
                $id ? __('Edit Sitemap') : __('New Sitemap')
            )
            ->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Sitemap_Edit'))
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
            // init model and set data
            /** @var Magento_Sitemap_Model_Sitemap $model */
            $model = Mage::getModel('Magento_Sitemap_Model_Sitemap');

            //validate path to generate
            if (!empty($data['sitemap_filename']) && !empty($data['sitemap_path'])) {
                $path = rtrim($data['sitemap_path'], '\\/')
                      . DS . $data['sitemap_filename'];
                /** @var $validator Magento_Core_Model_File_Validator_AvailablePath */
                $validator = Mage::getModel('Magento_Core_Model_File_Validator_AvailablePath');
                /** @var $helper Magento_Adminhtml_Helper_Catalog */
                $helper = Mage::helper('Magento_Adminhtml_Helper_Catalog');
                $validator->setPaths($helper->getSitemapValidPaths());
                if (!$validator->isValid($path)) {
                    foreach ($validator->getMessages() as $message) {
                        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($message);
                    }
                    // save data in session
                    Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData($data);
                    // redirect to edit form
                    $this->_redirect('*/*/edit', array(
                        'sitemap_id' => $this->getRequest()->getParam('sitemap_id')));
                    return;
                }
            }

            /** @var Magento_Filesystem $filesystem */
            $filesystem = $this->_objectManager->get('Magento_Filesystem');

            if ($this->getRequest()->getParam('sitemap_id')) {
                $model->load($this->getRequest()->getParam('sitemap_id'));
                $fileName = $model->getSitemapFilename();

                $filesystem->setWorkingDirectory(Mage::getBaseDir() . $model->getSitemapPath());
                $filePath = Mage::getBaseDir() . $model->getSitemapPath() . DS . $fileName;

                if ($fileName && $filesystem->isFile($filePath)) {
                    $filesystem->delete($filePath);
                }
            }

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('The sitemap has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('sitemap_id' => $model->getId()));
                    return;
                }
                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('sitemap_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array(
                    'sitemap_id' => $this->getRequest()->getParam('sitemap_id')));
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
        /** @var Magento_Filesystem $filesystem */
        $filesystem = $this->_objectManager->get('Magento_Filesystem');
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('sitemap_id');
        if ($id) {
            try {
                // init model and delete
                $model = Mage::getModel('Magento_Sitemap_Model_Sitemap');
                $model->setId($id);
                // init and load sitemap model

                /* @var $sitemap Magento_Sitemap_Model_Sitemap */
                $model->load($id);
                // delete file
                if ($model->getSitemapFilename() && $filesystem->isFile($model->getPreparedFilename())) {
                    $filesystem->delete($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('The sitemap has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('sitemap_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
            __('We can\'t find a sitemap to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Generate sitemap
     */
    public function generateAction()
    {
        // init and load sitemap model
        $id = $this->getRequest()->getParam('sitemap_id');
        $sitemap = Mage::getModel('Magento_Sitemap_Model_Sitemap');
        /* @var $sitemap Magento_Sitemap_Model_Sitemap */
        $sitemap->load($id);
        // if sitemap record exists
        if ($sitemap->getId()) {
            try {
                $sitemap->generateXml();

                $this->_getSession()->addSuccess(
                    __('The sitemap "%1" has been generated.', $sitemap->getSitemapFilename()));
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    __('Something went wrong generating the sitemap.'));
            }
        } else {
            $this->_getSession()->addError(
                __('We can\'t find a sitemap to generate.'));
        }

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
        return $this->_authorization->isAllowed('Magento_Sitemap::sitemap');
    }
}

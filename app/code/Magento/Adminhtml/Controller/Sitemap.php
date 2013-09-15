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
namespace Magento\Adminhtml\Controller;

class Sitemap extends  \Magento\Adminhtml\Controller\Action
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
     * @return \Magento\Adminhtml\Controller\Sitemap
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
        $model = \Mage::getModel('Magento\Sitemap\Model\Sitemap');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
                    __('This sitemap no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getSitemapFilename() : __('New Site Map'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('Magento\Adminhtml\Model\Session')->getFormData(true);
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
            ->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Sitemap\Edit'))
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
            /** @var \Magento\Sitemap\Model\Sitemap $model */
            $model = \Mage::getModel('Magento\Sitemap\Model\Sitemap');

            //validate path to generate
            if (!empty($data['sitemap_filename']) && !empty($data['sitemap_path'])) {
                $path = rtrim($data['sitemap_path'], '\\/')
                      . DS . $data['sitemap_filename'];
                /** @var $validator \Magento\Core\Model\File\Validator\AvailablePath */
                $validator = \Mage::getModel('Magento\Core\Model\File\Validator\AvailablePath');
                /** @var $helper \Magento\Adminhtml\Helper\Catalog */
                $helper = $this->_objectManager->get('Magento\Adminhtml\Helper\Catalog');
                $validator->setPaths($helper->getSitemapValidPaths());
                if (!$validator->isValid($path)) {
                    foreach ($validator->getMessages() as $message) {
                        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($message);
                    }
                    // save data in session
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
                    // redirect to edit form
                    $this->_redirect('*/*/edit', array(
                        'sitemap_id' => $this->getRequest()->getParam('sitemap_id')));
                    return;
                }
            }

            /** @var \Magento\Filesystem $filesystem */
            $filesystem = $this->_objectManager->get('Magento\Filesystem');

            if ($this->getRequest()->getParam('sitemap_id')) {
                $model->load($this->getRequest()->getParam('sitemap_id'));
                $fileName = $model->getSitemapFilename();

                $filesystem->setWorkingDirectory(\Mage::getBaseDir() . $model->getSitemapPath());
                $filePath = \Mage::getBaseDir() . $model->getSitemapPath() . DS . $fileName;

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
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('The sitemap has been saved.'));
                // clear previously saved data from session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData(false);

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

            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // save data in session
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setFormData($data);
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
        /** @var \Magento\Filesystem $filesystem */
        $filesystem = $this->_objectManager->get('Magento\Filesystem');
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('sitemap_id');
        if ($id) {
            try {
                // init model and delete
                $model = \Mage::getModel('Magento\Sitemap\Model\Sitemap');
                $model->setId($id);
                // init and load sitemap model

                /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
                $model->load($id);
                // delete file
                if ($model->getSitemapFilename() && $filesystem->isFile($model->getPreparedFilename())) {
                    $filesystem->delete($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('The sitemap has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (\Exception $e) {
                // display error message
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('sitemap_id' => $id));
                return;
            }
        }
        // display error message
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
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
        $sitemap = \Mage::getModel('Magento\Sitemap\Model\Sitemap');
        /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
        $sitemap->load($id);
        // if sitemap record exists
        if ($sitemap->getId()) {
            try {
                $sitemap->generateXml();

                $this->_getSession()->addSuccess(
                    __('The sitemap "%1" has been generated.', $sitemap->getSitemapFilename()));
            } catch (Magento_Core_Exception $e) {
            catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
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

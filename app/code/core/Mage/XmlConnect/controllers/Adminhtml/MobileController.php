<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Adminhtml_MobileController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize application
     * @param string $paramName
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _initApp($paramName = 'application_id', $applicationIdOverride = false)
    {
        if ($applicationIdOverride === false) {
            $id = (int) $this->getRequest()->getParam($paramName);
        } else {
            $id = (int) $applicationIdOverride;
        }
        $app = Mage::getModel('xmlconnect/application');
        if ($id) {
            $app->load($id);
            if (!$app->getId()) {
                Mage::throwException($this->__('Aplication with id "%s" no longer exists.', $id));
            }
            $app->loadConfiguration();
        }
        else {
            $app->loadDefaultConfiguration();
        }
        Mage::register('current_app', $app);
        return $app;
    }

    /**
     * Mobile applications management
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('mobile/app');
        $this->renderLayout();
    }

    /**
     * Create new app
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Submision Action, loads application data
     */
    public function submissionAction()
    {
        try {
            $app = $this->_initApp();
            if (!$app->getId()) {
                $this->_getSession()->addException($e, $this->__('No application provided.'));
                $this->_redirect('*/*/');
            }
            $app->loadSubmit();
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $app->addData($data);
            }
            $this->loadLayout();
            $this->_setActiveMenu('mobile/app');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Can\'t open submission form.'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Edit app form
     */
    public function editAction()
    {
        try {
            if ($this->getRequest()->getParam('store')) {
                $id = Mage::getModel('xmlconnect/application')->getIdByStoreId(
                    $this->getRequest()->getParam('application_id'),
                    $this->getRequest()->getParam('store')
                );
                $app = $this->_initApp(false, $id);
            } else {
                $app = $this->_initApp();
            }

            $app->loadSubmit();
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $app->addData($data);
            }
            $this->loadLayout();
            $this->_setActiveMenu('mobile/app');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Can\'t open application form.'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Submit POST application action
     */
    public function submissionPostAction()
    {
        $data = $this->getRequest()->getPost();
        try {
            $isError = false;
            /** @var $app Mage_XmlConnect_Model_Application */
            $app = $this->_initApp('key');
            $app->loadSubmit();
            if (!empty($_FILES)) {
                foreach ($_FILES as $field=>$file) {
                    if (!empty($file['name']) && is_scalar($file['name'])) {
                        $uploadedFiles[] = $app->handleUpload($field);
                    }
                }
            }

            $params = $app->prepareSubmitParams($data);
            $errors = $app->validateSubmit($params);
            if ($errors !== true) {
                foreach ($errors as $err) {
                    $this->_getSession()->addError($err);
                }
                $isError = true;
            }

            if (!$isError) {
                $app->processPostRequest();
                $history = Mage::getModel('xmlconnect/history');
                $history->setData(array(
                    'params' => $params,
                    'application_id' => $app->getId(),
                    'created_at' => Mage::getModel('core/date')->date(),
                    'store_id' => $app->getStoreId(),
                    'title' => isset($params['title']) ? $params['title'] : '',
                    'key' => isset($params['resubmission_activation_key']) ?
                        $params['resubmission_activation_key'] : $params['key'],
                ));
                $history->save();
                $app->getResource()->updateApplicationStatus($app->getId(),
                    Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS);
                $this->_getSession()->addSuccess($this->__('Application has been submitted.'));
            } else {
                $this->_redirect($this->getUrl('*/*/edit', array('application_id' => $app->getId())));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect($this->getUrl('*/*/edit', array('application_id' => $app->getId())));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Can\'t submit application.'));
            Mage::logException($e);
            $this->_redirect($this->getUrl('*/*/edit', array('application_id' => $app->getId())));
            $this->_redirect('*/*/');
        }
        $this->_redirect('*/*/');
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        $redirectBack = $this->getRequest()->getParam('back', false);
        $app = false;
        if ($data) {
            try {
                $app = $this->_initApp();

                // FIXME: ONLY FOR DEVELOPMENT
                if ($this->getRequest()->getParam('saveTheme', false)) {
                    Mage_XmlConnect_Model_Theme::savePost($this->getRequest()->getParam('saveTheme'), $data);
                    $this->_getSession()->addSuccess('Theme has been saved.');
                    $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
                    return;
                }
                // END FIXME

                $app->addData($app->preparePostData($data));
                if (!empty($_FILES)) {
                    foreach ($_FILES as $field=>$file) {
                        if (!empty($file['name']) && is_scalar($file['name'])) {
                            $app->handleUpload($field);
                        }
                    }
                }
                $app->save();
                $this->_getSession()->addSuccess($this->__('Application has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e, $this->__('Unable to save application.'));
                $redirectBack = true;
            }
        }
        if ($app && $redirectBack) {
            $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    public function previewHomeAction()
    {
        $this->_previewAction('preview_iframe_home');
    }

    public function previewCatalogAction()
    {
        $this->_previewAction('preview_iframe_catalog');
    }

    /**
     * Preview action
     */
    protected function _previewAction($block)
    {
        $redirectBack = false;
        $app = false;
        try {
            $app = $this->_initApp();
            $app->addData($app->preparePostData($this->getRequest()->getPost()));
            if (!empty($_FILES)) {
                foreach ($_FILES as $field=>$file) {
                    if (!empty($file['name']) && is_scalar($file['name'])) {
                        $app->handleUpload($field);
                    }
                }
            }
            $this->loadLayout(FALSE);
            $preview = $this->getLayout()->getBlock($block);
            $preview->setConf($app->getRenderConf());
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Unable to process preview.'));
            $redirectBack = true;
        }
        if ($app && $redirectBack) {
            $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        try {
            $app = $this->_initApp();
            $app->delete();
            $this->_getSession()->addSuccess($this->__('Application has been deleted.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Unable to find a banner to delete.'));
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('mobile');
    }

    public function historyAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('mobile/app');
        $this->renderLayout();
    }
    /**
     * Render apps grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->_setActiveMenu('mobile/app');
        $this->renderLayout();
    }
}

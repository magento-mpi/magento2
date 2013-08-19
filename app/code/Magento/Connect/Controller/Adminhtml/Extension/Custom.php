<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Extension controller
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Connect_Controller_Adminhtml_Extension_Custom extends Magento_Adminhtml_Controller_Action
{
    /**
     * Redirect to edit Extension Package action
     *
     */
    public function indexAction()
    {
        $this->_title(__('Package Extensions'));

        $this->_forward('edit');
    }

    /**
     * Edit Extension Package Form
     *
     */
    public function editAction()
    {
        $this ->_title(__('Extension'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Connect::system_extensions_custom');
        $this->renderLayout();
    }

    /**
     * Reset Extension Package form data
     *
     */
    public function resetAction()
    {
        Mage::getSingleton('Magento_Connect_Model_Session')->unsCustomExtensionPackageFormData();
        $this->_redirect('*/*/edit');
    }

    /**
     * Load Local Extension Package
     *
     */
    public function loadAction()
    {
        $packageName = base64_decode(strtr($this->getRequest()->getParam('id'), '-_,', '+/='));
        if ($packageName) {
            $session = Mage::getSingleton('Magento_Connect_Model_Session');
            try {
                $data = Mage::helper('Magento_Connect_Helper_Data')->loadLocalPackage($packageName);
                if (!$data) {
                    Mage::throwException(__('Something went wrong loading the package data.'));
                }
                $data = array_merge($data, array('file_name' => $packageName));
                $session->setCustomExtensionPackageFormData($data);
                $session->addSuccess(
                    __('The package %1 data has been loaded.', $packageName)
                );
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit');
    }

    /**
     * Save Extension Package
     *
     */
    public function saveAction()
    {
        $session = Mage::getSingleton('Magento_Connect_Model_Session');
        $p = $this->getRequest()->getPost();

        if (!empty($p['_create'])) {
            $create = true;
            unset($p['_create']);
        }

        if ($p['file_name'] == '') {
            $p['file_name'] = $p['name'];
        }

        $session->setCustomExtensionPackageFormData($p);
        try {
            $ext = Mage::getModel('Magento_Connect_Model_Extension');
            /** @var $ext Magento_Connect_Model_Extension */
            $ext->setData($p);
            if ($ext->savePackage()) {
                $session->addSuccess(__('The package data has been saved.'));
            } else {
                $session->addError(__('Something went wrong saving the package data.'));
                $this->_redirect('*/*/edit');
            }
            if (empty($create)) {
                $this->_redirect('*/*/edit');
            } else {
                $this->_forward('create');
            }
        } catch (Magento_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        } catch (Exception $e){
            $session->addException($e, __('Something went wrong saving the package.'));
            $this->_redirect('*/*');
        }
    }

    /**
     * Create new Extension Package
     *
     */
    public function createAction()
    {
        $session = Mage::getSingleton('Magento_Connect_Model_Session');
        try {
            $p = $this->getRequest()->getPost();
            $session->setCustomExtensionPackageFormData($p);
            $ext = Mage::getModel('Magento_Connect_Model_Extension');
            $ext->setData($p);
            $packageVersion = $this->getRequest()->getPost('version_ids');
            if (is_array($packageVersion)) {
                if (in_array(Magento_Connect_Package::PACKAGE_VERSION_2X, $packageVersion)) {
                    $ext->createPackage();
                }
                if (in_array(Magento_Connect_Package::PACKAGE_VERSION_1X, $packageVersion)) {
                    $ext->createPackageV1x();
                }
            }
            $this->_redirect('*/*');
        } catch(Magento_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        } catch(Exception $e){
            $session->addException($e, __('Something went wrong creating the package.'));
            $this->_redirect('*/*');
        }
    }

    /**
     * Load Grid with Local Packages
     *
     */
    public function loadtabAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid for loading packages
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Check is allowed access to actions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Adminhtml::custom');
    }
}

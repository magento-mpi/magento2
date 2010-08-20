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
    protected function _initApp($paramName = 'application_id')
    {
        $id = (int) $this->getRequest()->getParam($paramName);
        $app = Mage::getModel('xmlconnect/application');
        if ($id) {
            $app->load($id);
            if (!$app->getId()) {
                Mage::throwException(Mage::helper('xmlconnect')->__('Application with id "%s" no longer exists.', $id));
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
     * Restore data from session $_POST and $_FILES (processed)
     *
     * @param array $data
     * @return array|null
     */
    protected function _restoreSessionFilesFormData($data)
    {
        $filesData = Mage::getSingleton('adminhtml/session')->getUploadedFilesFormData(true);
        if (!empty($filesData) && is_array($filesData)) {
            if (!is_array($data)) {
                $data = array();
            }
            foreach ($filesData as $filePath => $fileName) {
                $target =& $data;
                $this->_injectFieldToArray($target, $filePath, $fileName);
            }
        }
        return $data;
    }


    /**
     * Set value into multidimensional array 'conf/native/navigationBar/icon'
     *
     * @param array     &$target                // pointer to target array
     * @param string    $fieldPath              //'conf/native/navigationBar/icon'
     * @param mixed     $fieldValue             // 'Some Value' || 12345 || array(1=>3, 'aa'=>43)
     * @param string    $delimiter              // path delimiter
     * @return null
     */
    protected function _injectFieldToArray(&$target, $fieldPath, $fieldValue, $delimiter = '/')
    {
        $nameParts = explode($delimiter, $fieldPath);
        foreach($nameParts as $next) {
            if (!isset($target[$next])) {
                $target[$next] = array();
            }
            $target =& $target[$next];
        }
        $target = $fieldValue;
        return null;
    }

    /**
     * Mobile applications management
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('xmlconnect/mobile');
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
                $this->_getSession()->addError(Mage::helper('xmlconnect')->__('No application provided.'));
                $this->_redirect('*/*/');
                return;
            }
            $app->loadSubmit();
            $data = $this->_restoreSessionFilesFormData(Mage::getSingleton('adminhtml/session')->getFormSubmissionData(true));
            if (!empty($data)) {
                $app->addData($data);
            }
            $this->loadLayout();
            $this->_setActiveMenu('xmlconnect/mobile');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('xmlconnect')->__('Can\'t open submission form.'));
            $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
        }
    }

    /**
     * Edit app form
     */
    public function editAction()
    {
        $redirectBack = false;
        try {
            $app = $this->_initApp();
            $app->loadSubmit();
            $data = $this->_restoreSessionFilesFormData(Mage::getSingleton('adminhtml/session')->getFormData(true));
            if (!empty($data)) {
                $app->addData($data);
            }
            $this->loadLayout();
            $this->_setActiveMenu('xmlconnect/mobile');
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('xmlconnect')->__('Unable to load application form.'));
            $redirectBack = true;
            Mage::logException($e);
        }
        if ($redirectBack) {
            $this->_redirect('*/*/');
            return;
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
            if (!empty($data)) {
                Mage::getSingleton('adminhtml/session')->setFormSubmissionData($data);
            }
            /** @var $app Mage_XmlConnect_Model_Application */
            $app = $this->_initApp('key');
            $app->loadSubmit();

            $app->addData($this->_processUploadedFiles($app->getData()));
            $params = $app->prepareSubmitParams($data);
            $errors = $app->validateSubmit($params);
            if ($errors !== true) {
                foreach ($errors as $err) {
                    $this->_getSession()->addError($err);
                }
                $isError = true;
            }
            if (!$isError) {
                $this->_processPostRequest();
                $history = Mage::getModel('xmlconnect/history');
                $history->setData(array(
                    'params' => $params,
                    'application_id' => $app->getId(),
                    'created_at' => Mage::getModel('core/date')->date(),
                    'store_id' => $app->getStoreId(),
                    'title' => isset($params['title']) ? $params['title'] : '',
                    'code' => $app->getCode(),
                    'activation_key' => isset($params['resubmission_activation_key']) ?
                        $params['resubmission_activation_key'] : $params['key'],
                ));
                $history->save();
                $app->getResource()->updateApplicationStatus($app->getId(),
                    Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS);
                $this->_getSession()->addSuccess(Mage::helper('xmlconnect')->__('Application has been submitted.'));
                $this->_clearSessionData();
                $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
            } else {
                $this->_redirect('*/*/submission', array('application_id' => $app->getId()));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/submission', array('application_id' => $app->getId()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('xmlconnect')->__('Can\'t submit application.'));
            Mage::logException($e);
            $this->_redirect('*/*/submission', array('application_id' => $app->getId()));
        }
    }

    /**
     * Clear session data
     * Used after succesfull save/submit action
     *
     * @return this
     */
    protected function _clearSessionData()
    {
        Mage::getSingleton('adminhtml/session')->unsFormData();
        Mage::getSingleton('adminhtml/session')->unsFormSubmissionData();
        Mage::getSingleton('adminhtml/session')->unsUploadedFilesFormData();
        return $this;
    }

    /**
     * Send HTTP POST request to magentocommerce.com
     *
     * @return void
     */
    protected function _processPostRequest()
    {
        try {
            $app = Mage::registry('current_app');
            $params = $app->getSubmitParams();

            $appConnectorUrl = Mage::getStoreConfig('xmlconnect/mobile_application/magentocommerce_url');
            $ch = curl_init($appConnectorUrl . $params['key']);

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            // Execute the request.
            $result = curl_exec($ch);
            $succeeded  = curl_errno($ch) == 0 ? true : false;

            // close cURL resource, and free up system resources
            curl_close($ch);

            // Assert that we received an expected message in reponse.
            $resultArray = json_decode($result, true);

            $app->setResult($result);
            $success = (isset($resultArray['success'])) && ($resultArray['success'] === true);

            $app->setSuccess($success);
            if (!$app->getSuccess()) {
                $message = '';
                $message = isset($resultArray['message']) ? $resultArray['message']: '';
                if (is_array($message)) {
                    $message = implode(' ,', $message);
                }
                Mage::throwException(Mage::helper('xmlconnect')->__('Submit Application failure. %s', $message));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        $redirectBack = $this->getRequest()->getParam('back', false);
        $redirectSubmit = $this->getRequest()->getParam('submitapp', false);
        $app = false;
        $isError = false;
        if ($data) {
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                $app = $this->_initApp();
                $app->addData($this->_preparePostData($data));
                $app->addData($this->_processUploadedFiles($app->getData()));
                $errors = $app->validate();
                if ($errors !== true) {
                    foreach ($errors as $err) {
                        $this->_getSession()->addError($err);
                    }
                    $isError = true;
                }
                if (!$isError) {
                    $this->_saveThemeAction($data, 'current_theme');
                    $app->save();
                    $this->_getSession()->addSuccess(Mage::helper('xmlconnect')->__('Application has been saved.'));
                    $this->_clearSessionData();
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $isError = true;
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('xmlconnect')->__('Unable to save application.'));
                $redirectBack = true;
                Mage::logException($e);
            }
        }
        if(!$isError && $app->getId() && $redirectSubmit) {
            $this->_redirect('*/*/submission', array('application_id' => $app->getId()));
        } else if ($isError || ($app->getId() && $redirectBack)) {
            $this->_redirect('*/*/edit', array('application_id' => $app->getId()));
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save changes to theme
     *
     * @param array     $data
     * @param string    $paramId
     */
    protected function _saveThemeAction($data, $paramId = 'saveTheme')
    {
        try {
            $themeName = $this->getRequest()->getParam($paramId, false);
            if ($themeName) {
                if ($themeName == Mage::helper('xmlconnect/theme')->getCustomThemeName()) {
                    $theme = Mage::helper('xmlconnect/theme')->getThemeByName($themeName);
                    if ($theme instanceof Mage_XmlConnect_Model_Theme) {
                        if ($paramId == 'saveTheme') {
                            $convertedConf = $this->_convertPost($data);
                        } else {
                            if (isset($data['conf'])) {
                                $convertedConf = $data['conf'];
                            } else {
                                $response = array('error' => true, 'message' => Mage::helper('xmlconnect')->__('Cannot save theme "%s". Incorrect data received', $themeName));
                            }
                        }
                        $theme->importAndSaveData($convertedConf);
                        $response = Mage::helper('xmlconnect/theme')->getAllThemesArray(true);
                    } else {
                        $response = array('error' => true, 'message' => Mage::helper('xmlconnect')->__('Cannot load theme "%s".', $themeName));
                    }
                } else {
                    $response = Mage::helper('xmlconnect/theme')->getAllThemesArray(true);
                }
            } else {
                $response = array('error' => true, 'message' => Mage::helper('xmlconnect')->__('Theme Name is not set.'));
            }
        }
        catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        }
        catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => Mage::helper('xmlconnect')->__('Cannot Save Theme.')
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Converts native Ajax data from flat to real array
     * Convert array key->value pairs inside array like:
     * "conf_native_bar_tintcolor" => $val   to   $conf['native']['bar']['tintcolor'] => $val
     *
     * @param array $data   $_POST
     * @return array
     */
    protected function _convertPost($data)
    {
        $conf = array();
        foreach($data as $key => $val){
            $parts = explode('_', $key);
            // "4" - is number of expected params conf_native_bar_tintcolor in correct data
            if (is_array($parts) && (count($parts) == 4)) {
                @list($key0, $key1, $key2, $key3) = $parts;
                if (!isset($conf[$key1])) {
                    $conf[$key1] = array();
                }
                if (!isset($conf[$key1][$key2])) {
                    $conf[$key1][$key2] = array();
                }
            $conf[$key1][$key2][$key3] = $val;
            }
        }
        return $conf;
    }

    /**
     * Save Theme action
     */
    public function saveThemeAction()
    {
        $data = $this->getRequest()->getPost();
        $response = false;
        $this->_saveThemeAction($data);
    }

    /**
     * Save Theme action
     */
    public function resetThemeAction()
    {
        $response = false;
        try {
            Mage::helper('xmlconnect/theme')->resetAllThemes();
            $response = Mage::helper('xmlconnect/theme')->getAllThemesArray(true);
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        }
        catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => Mage::helper('xmlconnect')->__('Cannot Reset Theme.')
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
            $this->getResponse()->setBody($response);
        }
    }

    /**
     * Preview Home action handler
     */
    public function previewHomeAction()
    {
        $this->_previewAction('preview_home_content');
    }

    /**
     * Preview Catalog action handler
     */
    public function previewCatalogAction()
    {
        $this->_previewAction('preview_catalog_content');
    }

    /**
     * Preview action implementation
     *
     * @param string    $block
     */
    protected function _previewAction($block)
    {
        $redirectBack = false;
        $app = false;
        try {
            $app = $this->_initApp();
            if (!$this->getRequest()->getParam('submission_action')) {
                $app->addData($this->_preparePostData($this->getRequest()->getPost()));
            }
            $app->addData($this->_processUploadedFiles($app->getData()));

            $this->loadLayout(FALSE);
            $preview = $this->getLayout()->getBlock($block);
            $preview->setConf($app->getRenderConf());
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
            $redirectBack = true;
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('xmlconnect')->__('Unable to process preview.'));
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
            if (!$app->getIsSubmitted()) {
                $app->delete();
                $this->_getSession()->addSuccess(Mage::helper('xmlconnect')->__('Application has been deleted.'));
            } else {
                Mage::throwException(Mage::helper('xmlconnect')->__('It\'s not allowed to delete submitted application.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('xmlconnect')->__('Unable to find an application to delete.'));
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

    /**
     * List application submit history
     */
    public function historyAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('xmlconnect/history');
        $this->renderLayout();
    }

    /**
     * Render apps grid
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->_setActiveMenu('xmlconnect/mobile');
        $this->renderLayout();
    }

    /**
     * Process all uploaded files
     * setup filenames to the configuration return array
     *
     * @return array
     */
    protected function _processUploadedFiles($data)
    {
        $this->_uploadedFiles = array();
        if (!empty($_FILES)) {
            foreach ($_FILES as $field => $file) {
                if (!empty($file['name']) && is_scalar($file['name'])) {
                    $this->_uploadedFiles[$field] = $this->_handleUpload($field, $data);
                }
            }
        }
        Mage::getSingleton('adminhtml/session')->setUploadedFilesFormData($this->_uploadedFiles);
        return $data;
    }

    /**
     * Process uploaded file
     * setup filenames to the configuration
     *
     * @param string $field
     */
    protected function _handleUpload($field, &$target)
    {
        $uploadedFilename = '';
        $upload_dir = Mage::getBaseDir('media') . DS . 'xmlconnect';
        $this->_forcedConvertPng($field);

        try {
            $uploader = new Varien_File_Uploader($field);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->save($upload_dir);
        } catch (Exception $e ) {
            /**
             * Hard coded exception catch
             */
            if ($e->getMessage() == 'Disallowed file type.') {
                $filename = $_FILES[$field]['name'];
                Mage::throwException(Mage::helper('xmlconnect')->__('Error while uploading file "%s". Disallowed file type. Only "jpg", "jpeg", "gif", "png" are allowed.', $filename));
            }
        }
        $uploadedFilename = $uploader->getUploadedFileName();
        $this->_injectFieldToArray($target, $field, $uploadedFilename);
        $this->_handleResize($field, $upload_dir . DS . $uploadedFilename);
        return $uploadedFilename;
    }

    /**
     * Convert uploaded file to PNG
     *
     * @param string $field
     */
    protected function _forcedConvertPng($field)
    {
        $file =& $_FILES[$field];

        $file['name'] = preg_replace('/\.(gif|jp[e]g)$/i', '.png', $file['name']);

        list($x, $x, $fileType) = getimagesize($file['tmp_name']);
        if ($fileType != IMAGETYPE_PNG ) {
            switch( $fileType ) {
                case IMAGETYPE_GIF:
                    $img = imagecreatefromgif($file['tmp_name']);
                    break;
                case IMAGETYPE_JPEG:
                    $img = imagecreatefromjpeg($file['tmp_name']);
                    break;
                default:
                    return;
            }
            imagepng($img, $file['tmp_name']);
            imagedestroy($img);
        }
    }

    /**
     * Resize uploaded file
     *
     * @param array $nameParts
     * @param string $file
     * @return void
     */
    protected function _handleResize($fieldPath, $file)
    {
        $nameParts = explode('/', $fieldPath);
        array_shift($nameParts);
        $app = Mage::registry('current_app');
        $conf = Mage::getStoreConfig('imageLimits/'.$app->getType());
        while (count($nameParts)) {
            $next = array_shift($nameParts);
            if (isset($conf[$next])) {
                $conf = $conf[$next];
            }
            /**
             * No config data - nothing to resize
             */
            else {
                return;
            }
        }

        $image = new Varien_Image($file);
        $width = $image->getOriginalWidth();
        $height = $image->getOriginalHeight();

        if (isset($conf['widthMax']) && ($conf['widthMax'] < $width)) {
            $width = $conf['widthMax'];
        }
        elseif (isset($conf['width'])) {
            $width = $conf['width'];
        }

        if (isset($conf['heightMax']) && ($conf['heightMax'] < $height)) {
            $height = $conf['heightMax'];
        }
        elseif (isset($conf['height'])) {
            $height = $conf['height'];
        }

        if (($width != $image->getOriginalWidth()) ||
            ($height != $image->getOriginalHeight()) ) {
            $image->resize($width, $height);
            $image->save(null, basename($file));
        }
    }

    /**
     * Prepare post data
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return array
     */
    public function _preparePostData(array $arr)
    {
        unset($arr['code']);
        if (isset($arr['conf']['new_pages']) && isset($arr['conf']['new_pages']['ids'])
            && isset($arr['conf']['new_pages']['labels'])) {

            $new_pages = array();
            foreach ($arr['conf']['new_pages']['ids'] as $key=>$value) {
                $new_pages[$key]['id'] = trim($value);
            }
            foreach ($arr['conf']['new_pages']['labels'] as $key=>$value) {
                $new_pages[$key]['label'] = trim($value);
            }
            if (!isset($arr['conf']['native']['pages'])) {
                $arr['conf']['native']['pages'] = array();
            }
            foreach ($new_pages as $key => $page) {
                if (empty($page['id']) || empty($page['label'])) {
                    unset($new_pages[$key]);
                }
            }
            if (!empty($new_pages)) {
                $arr['conf']['native']['pages'] = array_merge($arr['conf']['native']['pages'], $new_pages);
            }
            unset($arr['conf']['new_pages']);
        }

        if (!isset($arr['conf']['defaultCheckout'])) {
            $arr['conf']['defaultCheckout'] = array();
        }
        if (!isset($arr['conf']['defaultCheckout']['isActive'])) {
            $arr['conf']['defaultCheckout']['isActive'] = 0;
        }

        if (!isset($arr['conf']['paypal'])) {
            $arr['conf']['paypal'] = array();
        }
        if (!isset($arr['conf']['paypal']['isActive'])) {
            $arr['conf']['paypal']['isActive'] = 0;
        }
        return $arr;
    }

    /**
     * Submission history grid action on submission history tab
     */
    public function submissionHistoryGridAction()
    {
        $this->_initApp();
        $this->loadLayout();
        $this->renderLayout();
    }
}



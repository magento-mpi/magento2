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
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
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
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
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
                Mage::getSingleton('adminhtml/session')->setFormData($data);
            }
            /** @var $app Mage_XmlConnect_Model_Application */
            $app = $this->_initApp('key');
            $app->loadSubmit();

            $app->addData(array('conf' => $this->_processUploadedFiles($app->getConf())));

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
                    'activation_key' => isset($params['resubmission_activation_key']) ?
                        $params['resubmission_activation_key'] : $params['key'],
                ));
                $history->save();
                $app->getResource()->updateApplicationStatus($app->getId(),
                    Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS);
                $this->_getSession()->addSuccess(Mage::helper('xmlconnect')->__('Application has been submitted.'));
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
        if ($data) {
            try {
                $app = $this->_initApp();
                $this->_saveThemeAction($data, 'current_theme');
                $app->addData($this->_preparePostData($data));
                $app->addData(array('conf' => $this->_processUploadedFiles($app->getConf())));
                $app->save();
                $this->_getSession()->addSuccess(Mage::helper('xmlconnect')->__('Application has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addException($e, $e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('xmlconnect')->__('Unable to save application.'));
                $redirectBack = true;
                Mage::logException($e);
            }
        }
        if($app->getId() && $redirectSubmit){
            $this->_redirect('*/*/submission', array('application_id' => $app->getId()));
        }
        else if ($app->getId() && $redirectBack) {
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
            $app->addData(array('conf' => $this->_processUploadedFiles($app->getConf())));

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
    protected function _processUploadedFiles($conf)
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $field=>$file) {
                if (!empty($file['name']) && is_scalar($file['name'])) {
                    $this->_handleUpload($field, $conf);
                }
            }
        }
        return $conf;
    }

    /**
     * Process uploaded file
     * setup filenames to the configuration
     *
     * @param string $field
     */
    protected function _handleUpload($field, &$target)
    {
        $upload_dir = Mage::getBaseDir('media') . DS . 'xmlconnect';

        $this->_forcedConvertPng($field);

        $uploader = new Varien_File_Uploader($field);
        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
        $uploader->setAllowRenameFiles(true);
        $uploader->save($upload_dir);

        /**
         * Ugly hack to avoid $_FILES[..]['name'][..][..]
         *
         * e.g., variable name in $_POST: 'conf/native/navigationBar/icon' ==>
         * file name stored in $this->_data['conf']['native']['navigationBar']['icon']
         * here icon - filename like 'logo_23.gif'
         */
        $nameParts = explode('/', $field);
        array_shift($nameParts);
        foreach($nameParts as $next) {
            if (!isset($target[$next])) {
                $target[$next] = array();
            }
            $target =& $target[$next];
        }
        $target = $uploader->getUploadedFileName();

        $this->_handleResize($nameParts, $upload_dir . DS . $uploader->getUploadedFileName());
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
    protected function _handleResize($nameParts, $file)
    {
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
                $new_pages[$key]['id'] = $value;
            }
            foreach ($arr['conf']['new_pages']['labels'] as $key=>$value) {
                $new_pages[$key]['label'] = $value;
            }
            if (!isset($arr['conf']['native']['pages'])) {
                $arr['conf']['native']['pages'] = array();
            }
            $arr['conf']['native']['pages'] = array_merge($arr['conf']['native']['pages'], $new_pages);
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



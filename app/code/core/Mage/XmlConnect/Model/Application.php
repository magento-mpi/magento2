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
class Mage_XmlConnect_Model_Application extends Mage_Core_Model_Abstract
{

    /**
     * Application code cookie name
     */
    const APP_CODE_COOKIE_NAME = 'app_code';

    /**
     * Application status succes value
     *
     * @var int
     */
    const APP_STATUS_SUCCESS = 1;

    /**
     * Application prefix length of cutted part of deviceType and storeCode
     *
     * @var int
     */
    const APP_PREFIX_CUT_LENGTH = 3;

    /**
     * Images in "Params" history table
     *
     * @var array
     */
    protected $_imageIds = array('icon', 'loader_image', 'logo', 'big_logo');


    /**
     * Initialize application
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/application');
    }

    /**
     * Prepare post data
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return array
     */
    public function preparePostData(array $arr)
    {
        if (isset($arr['code'])) {
            unset($arr['code']);
        }
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
        return $arr;
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @return array
     */
    public function getFormData()
    {
        $data = $this->getData();
        return $this->_flatArray($data);
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @param array $subtree
     * @param string $prefix
     * @return array
     */
    protected function _flatArray($subtree, $prefix=null)
    {
        $result = array();
        foreach ($subtree as $key => $value) {
            if (is_null($prefix)) {
                $name = $key;
            }
            else {
                $name = $prefix . '[' . $key . ']';
            }

            if (is_array($value)) {
                $result = array_merge($result, $this->_flatArray($value, $name));
            }
            else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * Like array_merge_recursive(), but string values is replaced
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    protected function _configMerge (array $a, array $b)
    {
        $result = array();
        $keys = array_unique(array_merge(array_keys($a), array_keys($b)));
        foreach ($keys as $key) {
            if (!isset($a[$key])) {
                $result[$key] = $b[$key];
            }
            elseif (!isset($b[$key])) {
                $result[$key] = $a[$key];
            }
            elseif (is_scalar($a[$key]) || is_scalar($b[$key])) {
                $result[$key] = $b[$key];
            }
            else {
                $result[$key] = $this->_configMerge($a[$key], $b[$key]);
            }
        }
        return $result;
    }

    public function loadDefaultConfiguration()
    {
        $this->setType('iphone');
        $this->setCode($this->getCodePrefix());
        $this->setConf(Mage::helper('xmlconnect/iphone')->getDefaultConfiguration());
    }

    /**
     * Return first part for application code field
     */
    public function getCodePrefix()
    {
        return substr(Mage::app()->getStore($this->getStoreId())->getCode(), 0, self::APP_PREFIX_CUT_LENGTH)
            . substr($this->getType(), 0, self::APP_PREFIX_CUT_LENGTH);
    }

    /**
     * Checks if application code field has autoincrement
     */
    public function isCodePrefixed()
    {
        $suffix = substr($this->getCode(), self::APP_PREFIX_CUT_LENGTH * 2);
        return !empty($suffix);
    }

    /**
     * Load application configuration
     *
     * @return array
     */
    public function prepareConfiguration()
    {
        return $this->getData('conf');
    }

    public function getRenderConf()
    {
        $result = Mage::helper('xmlconnect/iphone')->getDefaultConfiguration();
        $result = $result['native'];
        $extra = array();
        if (isset($this->_data['conf'])) {
            if (isset($this->_data['conf']['native'])) {
                $result = $this->_configMerge($result, $this->_data['conf']['native']);
            }
            if (isset($this->_data['conf']['extra'])) {
                $extra = $this->_data['conf']['extra'];
                if (isset($extra['tabs'])) {
                    $tabs = new Mage_XmlConnect_Model_Tabs($extra['tabs']);
                    $result['tabBar']['tabs'] = $tabs;
                }
                if (isset($extra['fontColors'])) {
                    if (!empty($extra['fontColors']['header'])) {
                        $result['fonts']['Title1']['color'] = $extra['fontColors']['header'];
                    }
                    if (!empty($extra['fontColors']['primary'])) {
                        $result['fonts']['Title2']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Title3']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Text1']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Text2']['color'] = $extra['fontColors']['primary'];
                    }
                    if (!empty($extra['fontColors']['secondary'])) {
                        $result['fonts']['Title4']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title6']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title8']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title7']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title9']['color'] = $extra['fontColors']['secondary'];
                    }
                    if (!empty($extra['fontColors']['price'])) {
                        $result['fonts']['Title5']['color'] = $extra['fontColors']['price'];
                    }
                }
            }
        }
        $result = $this->_absPath($result);

        $result['updateTimeUTC'] = strtotime($this->getUpdatedAt());
        $result['currencyCode'] = Mage::app()->getStore($this->getStoreId())->getDefaultCurrencyCode();
        $result['secureBaseUrl'] = Mage::getStoreConfig('web/secure/base_url', $this->getStoreId());

        $maxRecepients = 0;
        if ( Mage::getStoreConfig('sendfriend/email/enabled') ) {
            $maxRecepients = Mage::getStoreConfig('sendfriend/email/max_recipients');
        }
        $result['emailToFriend']['maxRecepients'] = $maxRecepients;

        $result['paypal']['businessAccount'] = Mage::getStoreConfig('paypal/general/business_account');
        $result['paypal']['merchantLabel'] = $this->getData('conf/special/merchantLabel');

        return $result;
    }

    /**
     * Return Enabled Tabs array from actual config
     *
     * @return array:
     */
    public function getEnabledTabsArray()
    {
        $tabs = array();
        $tabsObject = null;
        if (isset($this->_data['conf'])) {
            if (isset($this->_data['conf']['extra'])) {
                $extra = $this->_data['conf']['extra'];
                if (isset($extra['tabs'])) {
                    $tabsObject = new Mage_XmlConnect_Model_Tabs($extra['tabs']);
                }
            }
        }
        if ($tabsObject instanceof Mage_XmlConnect_Model_Tabs) {
            $tabs = $tabsObject->getRenderTabs();
        }
        return $tabs;
    }

    /**
     * Change URLs to absolue
     *
     * @param array $subtree
     * @return array
     */
    protected function _absPath($subtree)
    {
        foreach ($subtree as $key => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    $subtree[$key] = $this->_absPath($value);
                }
                elseif ((substr($key, -4) == 'icon') ||
                    (substr($key, -4) == 'Icon') ||
                    (substr($key, -5) == 'Image')) {
                    $subtree[$key] = Mage::getBaseUrl('media') . 'xmlconnect/' . $value;
                }
            }
        }
        return $subtree;
    }

    /**
     * Return content pages
     *
     * @return array
     */
    public function getPages()
    {
        if (isset($this->_data['conf']['native']['pages'])) {
            return $this->_data['conf']['native']['pages'];
        }
        return array();
    }

    /**
     * Processing object before save data
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _beforeSave()
    {
        $conf = serialize($this->prepareConfiguration());
        $this->setConfiguration($conf);
        $this->setUpdatedAt(date('Y-m-d H:i:s', time()));
        return $this;
    }

    /**
     * Load configuration data (from serialized blob)
     *
     * @return Mage_XmlConnect_Model_Application
     */
    public function loadConfiguration()
    {
        $configuration = $this->getConfiguration();
        if (!empty($configuration)) {
            $configuration = unserialize($configuration);
            $this->setData('conf', $configuration);
        }
        return $this;
    }

    /**
     * Process uploaded file
     * setup filenames to the configuration
     *
     * @param string $field
     */
    public function handleUpload($field)
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
        $target =& $this->_data['conf'];
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
     */
    protected function _handleResize($nameParts, $file)
    {
        $conf = Mage::getStoreConfig('imageLimits/'.$this->getType());
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
     * Load application by code
     *
     * @param   string $code
     * @return  Mage_XmlConnect_Model_Application
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Loads submit tab data from xmlconnect/history table
     *
     * @return bool
     */
    public function loadSubmit()
    {
        $isResubmitAction = false;
        if ($this->getId()) {
            $params = $this->getLastParams();
            if (!empty($params)) {
                // Using Pointer !
                $conf = &$this->_data['conf'];
                if (!isset($conf['submit_text']) || !is_array($conf['submit_text'])) {
                    $conf['submit_text'] = array();
                }
                if (!isset($conf['submit_restore']) || !is_array($conf['submit_restore'])) {
                    $conf['submit_restore'] = array();
                }
                foreach ($params as $id => $value) {
                    if (!in_array($id, $this->_imageIds)) {
                        $conf['submit_text'][$id] = $value;
                    } else {
                        $conf['submit_restore'][$id] = $value;
                    }
                    $isResubmitAction = true;
                }
            }
        }
        $this->setIsResubmitAction($isResubmitAction);
        return $isResubmitAction;
    }

    /**
     * Returns ( image[ ID ] => "SRC" )  array
     *
     * @return array
     */
    public function getImages()
    {
        $images = array();
        $params = $this->getLastParams();

        if (!empty($params)) {
            foreach ($this->_imageIds as $id) {
                if (isset($params[$id])) {
                    $path = substr($params[$id], 1);
                    // converting :  @D:\wamp\www4\media\xmlconnect\form_icon_6.png
                    // to  http://locahost.com/media/xmlconnect/forn_icon_6.png
                    $images['conf/submit/'.$id] = Mage::getBaseUrl('media').'xmlconnect/'.basename(substr($params[$id], 1));
                }
            }
        }
        return $images;
    }

    /**
     * Return last submited data from history table
     *
     * @return array
     */
    public function getLastParams() {
        if (!isset($this->_lastParams)) {
            $this->_lastParams = Mage::getModel('xmlconnect/history')->getLastParams($this->getId());
        }
        return $this->_lastParams;
    }


    /**
     * Validate submit application data
     *
     * @return array|bool
     */
    public function validateSubmit($params)
    {
        $errors = array();
        $validateConf = $this->_validateConf();
        if ($validateConf !== true) {
            $errors = $validateConf;
        }
        $helper = Mage::helper('xmlconnect');
        if (!Zend_Validate::is(isset($params['title']) ? $params['title'] : null, 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the Title.');
        }

        if (!Zend_Validate::is(isset($params['copyright']) ? $params['copyright'] : null, 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the Copyright.');
        }

        if (empty($params['price_free'])) {
            if (!Zend_Validate::is(isset($params['price']) ? $params['price'] : null, 'NotEmpty')) {
                $errors[] = $helper->__('Please enter the Price.');
            }
        }

        if ($this->getIsResubmitAction()) {
            if (!Zend_Validate::is(
                    isset($params['resubmission_activation_key']) ? $params['resubmission_activation_key'] : null,
                    'NotEmpty')) {
                $errors[] = $helper->__('Please enter the Resubmission Key.');
            }
        } else {
            if (!Zend_Validate::is(isset($params['key']) ? $params['key'] : null, 'NotEmpty')) {
                    $errors[] = $helper->__('Please enter the Activation Key.');
            }
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    protected function _validateConf()
    {
        $errors = array();
        $helper = Mage::helper('xmlconnect');
        $conf = $this->getConf();
        $native = isset($conf['native']) && is_array($conf['native']) ? $conf['native'] : false;

        if ( ($native === false)
            || (!isset($native['navigationBar']) || !is_array($native['navigationBar'])
            || !isset($native['navigationBar']['icon'])
            || !Zend_Validate::is($native['navigationBar']['icon'], 'NotEmpty'))) {
            $errors[] = $helper->__('Please enter "Logo in header" on Desing Tab, and save Application before submit.');
        }

        if ( ($native === false)
            || (!isset($native['body']) || !is_array($native['body'])
            || !isset($native['body']['bannerImage'])
            || !Zend_Validate::is($native['body']['bannerImage'], 'NotEmpty'))) {
            $errors[] = $helper->__('Please enter "Banner on Home Screen" on Desing Tab, and save Application before submit.');
        }

        if (($native === false)
            || (!isset($native['body']) || !is_array($native['body'])
            || !isset($native['body']['backgroundImage'])
            || !Zend_Validate::is($native['body']['backgroundImage'], 'NotEmpty'))) {
            $errors[] = $helper->__('Please enter "Application Background" on Desing Tab, and save Application before submit.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Imports post/get data into the model
     *
     * @param array $data    - $_REQUEST[]
     *
     * @return array
     */
    public function prepareSubmitParams($data) {

        $params = array();
        if (isset($data['conf']) && is_array($data['conf'])) {

            if (isset($data['conf']['submit_text']) && is_array($data['conf']['submit_text'])) {
                $params = $data['conf']['submit_text'];
            }

            $params['name'] = $this->getName();
            $params['code'] = $this->getCode();
            $params['type'] = $this->getType();
            $params['url'] = Mage::helper('xmlconnect/data')->getUrl('*/*', array('code' => $this->getCode()));

            if (isset($params['country']) && is_array($params['country'])) {
                $params['country'] = implode(',', $params['country']);
            } else {
                Mage::throwException(Mage::helper('xmlconnect')->__('Please select at least one Country.'));
            }
            if ($this->getIsResubmitAction()) {
                $params['key'] = $params['resubmission_activation_key'];
            }
            // processing files :
            $submit = array();
            if (isset($this->_data['conf']['submit']) && is_array($this->_data['conf']['submit'])) {
                 $submit = $this->_data['conf']['submit'];
            }

            $submitRestore  = array();
            if (isset($this->_data['conf']['submit_restore']) && is_array($this->_data['conf']['submit_restore'])) {
                $submitRestore = $this->_data['conf']['submit_restore'];
            }

            $dir = Mage::getBaseDir('media') . DS . 'xmlconnect' . DS;

            foreach ($this->_imageIds as $id) {
                if (isset($submit[$id])) {
                    $params[$id] = '@' . $dir . $submit[$id];
                } else if (isset($submitRestore[$id])) {
                    $params[$id] = $submitRestore[$id];
                }
            }
        }
        $this->setSubmitParams($params);
        return $params;
    }

    /**
     * Send HTTP POST request to magentocommerce.com
     *
     * @param array $params
     *
     * @throws Exception
     */
    public function processPostRequest()
    {
        try {
            $params = $this->getSubmitParams();

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

            $this->setResult($result);
            $success = (isset($resultArray['success'])) && ($resultArray['success'] === true);

            $this->setSuccess($success);
            if (!$this->getSuccess()) {
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
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Getter, returns activation key for current application
     *
     * @return string|null
     */
    public function getActivationKey()
    {
        $key = null;
        if (isset($this->_data['conf']) && is_array($this->_data['conf']) &&
            isset($this->_data['conf']['submit_text']) && is_array($this->_data['conf']['submit_text']) &&
            isset($this->_data['conf']['submit_text']['key'])) {

            $key = $this->_data['conf']['submit_text']['key'];
        }
        return $key;
    }


    /**
     * Returns ApplicationId by applicationId and storeId for save storeId as current Application
     *
     * @param int   $applicationId
     * @param int   $storeId
     *
     * @return string
     */
    public function getIdByStoreId($applicationId, $storeId)
    {
        return $this->_getResource()->getIdByStoreId($this, $applicationId, $storeId);
    }
}

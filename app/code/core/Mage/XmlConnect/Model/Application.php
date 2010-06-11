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
        if (isset($arr['conf']['new_pages'])) {
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

    public function loadDefaultConfiguration()
    {
        $this->setType('iPhone');
        $this->setConf(Mage::helper('xmlconnect/iphone')->getDefaultConfiguration());
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
        $result = $extra = array();
        if (isset($this->_data['conf'])) {
            if (isset($this->_data['conf']['native'])) {
                $result = $this->_data['conf']['native'];
            }
            if (isset($this->_data['conf']['extra'])) {
                $extra = $this->_data['conf']['extra'];
                $tabs = new Mage_XmlConnect_Model_Tabs($extra['tabs']);
                $result['tabBar']['tabs'] = $tabs;
            }
        }

        return $result;
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
}

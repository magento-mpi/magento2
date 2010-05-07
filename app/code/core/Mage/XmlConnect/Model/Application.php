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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Model_Application extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('xmlconnect/application');
    }

    /**
     * Load application configuration
     *
     * @return array
     */
    public function prepareConfiguration()
    {
        $conf = array();
        $keys = array_keys($this->_data);
        foreach ($keys as $key) {
            if (substr($key, 0, 5) == 'conf/') {
                $conf[substr($key, 5)] = $this->_data[$key];
            }
        }
        return $conf;
    }

    /**
     * Load pre-set application configuration
     *
     * @return array
     */
    public function loadDefaultConfiguration()
    {
        $conf = Mage::getStoreConfig('defaultConfiguration');
        $conf = $this->_getFlatConfig($conf, 'conf/');
        $this->addData($conf);
        return TRUE;
    }

    /**
     * Merge configuration tree into flat array
     *
     * @param  array $config
     * @param  string $prefix
     * @return array
     */
    private function _getFlatConfig($config, $prefix = '') {
        $result = array();
        foreach ($config as $key => $value) {
            if (is_scalar($value)) {
                $result[$prefix . $key] = $value;
            }
            else {
                $child = $this->_getFlatConfig($value, $prefix . $key . '/');
                $result = array_merge($result, $child);
            }
        }
        return $result;
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
            if (is_array($configuration)) {
                foreach($configuration as $key => $value) {
                    $this->setData('conf/' . $key, $value);
                }
            }
        }
        return $this;
    }

    /**
     * Process uploaded file
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

        $this->setData($field, $uploader->getUploadedFileName());

        $this->_handleResize($field, $upload_dir . DS . $uploader->getUploadedFileName());
    }

    /**
     * Resize uploaded file
     *
     * @param string $field
     * @param string $file
     */
    protected function _handleResize($field, $file)
    {
        $conf = Mage::getStoreConfig('imageLimits');
        $nameParts = explode('/', $field);
        array_shift($nameParts);
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

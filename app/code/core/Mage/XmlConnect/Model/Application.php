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
        $this->_data += $conf;
        return TRUE;
    }

    private function _getFlatConfig($config, $prefix='') {
        $result = array();
        foreach ($config as $key=>$value) {
            if (is_scalar($value)) {
                $result[$prefix.$key] = $value;
            } else {
                $result += $this->_getFlatConfig($value, $prefix.$key.'/');
            }
        }
        return $result;
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->_data['configuration'] = serialize($this->prepareConfiguration());
        $this->_data['updated_at'] = date('Y-m-d H:i:s',time());
        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        if (!empty($this->_data['configuration'])) {
            $conf = unserialize($this->_data['configuration']);
            if (is_array($conf)) {
                foreach($conf as $key=>$value) {
                    $this->_data['conf/'.$key] = $value;
                }
            }
        }
        return $this;
    }

    public function handleUpload($field)
    {
        $uploader = new Varien_File_Uploader($field);
        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
        $uploader->setAllowRenameFiles(true);
        $uploader->save(Mage::getBaseDir('media').DS.'xmlconnect');
        $this->_data[$field] = $uploader->getUploadedFileName();
        $this->_handleResize($field, Mage::getBaseDir('media').DS.'xmlconnect'.DS.$this->_data[$field]);
    }

    protected function _handleResize($field, $file)
    {
        $conf = Mage::getStoreConfig('imageLimits');
        $nameParts = explode('/', $field);
        array_shift($nameParts);
        while (count($nameParts)) {
            $next = array_shift($nameParts);
            if (isset($conf[$next])) {
                $conf = $conf[$next];
            } else {
                return; // no config data - nothing to resize
            }
        }
        $image = new Varien_Image($file);
        $width = $image->getOriginalWidth();
        $height = $image->getOriginalHeight();
        if (isset($conf['widthMax']) && $conf['widthMax']<$width) {
            $width = $conf['widthMax'];
        } elseif (isset($conf['width'])) {
            $width = $conf['width'];
        }
        if (isset($conf['heightMax']) && $conf['heightMax']<$height) {
            $height = $conf['heightMax'];
        } elseif (isset($conf['height'])) {
            $height = $conf['height'];
        }
        if (($width!=$image->getOriginalWidth()) ||
            ($height!=$image->getOriginalHeight()) ) {
            $image->resize($width, $height);
            $image->save(null, basename($file));
        }
    }
}

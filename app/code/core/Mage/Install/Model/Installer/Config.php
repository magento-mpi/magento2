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
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config installer
 */
class Mage_Install_Model_Installer_Config
{
    const TMP_INSTALL_DATE_VALUE= 'd-d-d-d-d';
    const TMP_ENCRYPT_KEY_VALUE = 'k-k-k-k-k';

    /**
     * Path to local configuration file
     *
     * @var string
     */
    protected $_localConfigFile;

    protected $_configData = array();

    public function __construct()
    {
        $this->_localConfigFile = Mage::getBaseDir('etc').DS.'local.xml';
    }

    public function setConfigData($data)
    {
        if (is_array($data)) {
            $this->_configData = $data;
        }
        return $this;
    }

    public function getConfigData()
    {
        return $this->_configData;
    }

    public function install()
    {
        $data = $this->getConfigData();
        foreach (Mage::getModel('core/config')->getDistroServerVars() as $index=>$value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }
        $data['base_path'] .= substr($data['base_path'],-1) != '/' ? '/' : '';
        $data['secure_base_path'] .= substr($data['secure_base_path'],-1) != '/' ? '/' : '';

        if (!Mage::getSingleton('install/session')->getSkipUrlValidation()) {
            $this->_checkHostsInfo($data);
        }
        $data['date']   = self::TMP_INSTALL_DATE_VALUE;
        $data['key']    = self::TMP_ENCRYPT_KEY_VALUE;
        $data['var_dir'] = $data['root_dir'] . '/var';

        Mage::getSingleton('install/session')->setConfigData($data);

        file_put_contents($this->_localConfigFile, Mage::getModel('core/config')->getLocalDist($data));
        chmod($this->_localConfigFile, 0777);

        Mage::getConfig()->init();

        $cacheData = serialize(array(
            'config'     => 1,
            'layout'     => 1,
            'block_html' => 1,
            'eav'        => 1,
            'translate'  => 1,
            'pear'       => 1,
        ));
        Mage::app()->saveCache($cacheData, 'use_cache', array(), null);
    }

    public function getFormData()
    {
        $data = new Varien_Object();
        $host = $_SERVER['HTTP_HOST'];
        $hostInfo = explode(':', $host);
        $host = $hostInfo[0];
        $port = !empty($hostInfo[1]) ? $hostInfo[1] : 80;
        $basePath = dirname($_SERVER['SCRIPT_NAME']);

        $data->setServerPath(dirname(Mage::getBaseDir()))
            ->setHost($host)
            ->setBasePath($basePath)
            ->setPort($port)
            ->setSecureHost($host)
            ->setSecureBasePath($basePath)
            ->setSecurePort(443)
            ->setDbHost('localhost')
            ->setDbName('magento')
            ->setDbUser('root')
            ->setDbPass('')
            ->setUseScriptName(1);
        return $data;
    }

    protected function _checkHostsInfo($data)
    {
        $url = $data['protocol'] . '://' . $data['host'] . ':' . $data['port'] . $data['base_path'];
        $surl= $data['secure_protocol'] . '://' . $data['secure_host'] . ':' . $data['secure_port'] . $data['secure_base_path'];

        $this->_checkUrl($url);
        $this->_checkUrl($surl, true);

        return $this;
    }

    protected function _checkUrl($url, $secure=false)
    {
        $prefix = $secure ? 'install/wizard/checkSecureHost/' : 'install/wizard/checkHost/';
        $client = new Varien_Http_Client($url.$prefix);
        try {
            $response = $client->request('GET');
            /* @var $responce Zend_Http_Response */
            $body = $response->getBody();
        }
        catch (Exception $e){
            Mage::getSingleton('install/session')->addError(__('Url "%s" is not accessible', $url));
            throw $e;
        }

        if ($body != Mage_Install_Model_Installer::INSTALLER_HOST_RESPONSE) {
            Mage::getSingleton('install/session')->addError(__('Url "%s" is invalid', $url));
            Mage::throwException(__('This Url is invalid'));
        }
        return $this;
    }

    public function replaceTmpInstallDate($date = null)
    {
        if (is_null($date)) {
            $date = date('r');
        }
        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace(self::TMP_INSTALL_DATE_VALUE, date('r'), $localXml);
        file_put_contents($this->_localConfigFile, $localXml);

        return $this;
    }

    public function replaceTmpEncryptKey($key = null)
    {
        if (!$key) {
            $key = md5(time());
        }
        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace(self::TMP_ENCRYPT_KEY_VALUE, $key, $localXml);
        file_put_contents($this->_localConfigFile, $localXml);

        return $this;
    }
}
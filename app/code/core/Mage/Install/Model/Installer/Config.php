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
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Config
{
    protected $_localConfigFile;

    protected $_encryptKey;

    public function __construct()
    {
        $this->_localConfigFile = Mage::getBaseDir('etc').DS.'local.xml';
    }

    public function install()
    {
        $data = Mage::getSingleton('install/session')->getConfigData();
        foreach (Mage::getModel('core/config')->getDistroServerVars() as $index=>$value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }
        $this->_checkHostsInfo($data);
        $data['date'] = 'd-d-d-d-d';
        $data['key'] = 'k-k-k-k-k';
        $data['var_dir'] = $data['root_dir'] . '/var';
        file_put_contents($this->_localConfigFile, Mage::getModel('core/config')->getLocalDist($data));
        Mage::getConfig()->init();
    }

    public function setInstalled()
    {
        $localXml = file_get_contents($this->_localConfigFile);
        $localXml = str_replace('d-d-d-d-d', date('r'), $localXml);
        $localXml = str_replace('k-k-k-k-k', $this->_encryptKey, $localXml);
        file_put_contents($this->_localConfigFile, $localXml);
    }

    public function getFormData()
    {
        $data = new Varien_Object();
        $host = $_SERVER['HTTP_HOST'];
        $hostInfo = explode(':', $host);
        $host = $hostInfo[0];
        $port = !empty($hostInfo[1]) ? $hostInfo[1] : 80;

        $data->setServerPath(dirname(Mage::getBaseDir()))
            ->setHost($host)
            ->setBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setSecureHost($host)
            ->setSecureBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setPort($port)
            ->setSecurePort(443)
            ->setDbHost('localhost')
            ->setDbName('magento')
            ->setDbUser('root')
            ->setDbPass('');
        return $data;
    }

    protected function _checkHostsInfo($data)
    {
        $url = $data['protocol'] . '://' . $data['host'] . ':' . $data['port'] . $data['base_path'];
        $surl= $data['secure_protocol'] . '://' . $data['secure_host'] . ':' . $data['secure_port'] . $data['secure_base_path'];

        $reporting_level = error_reporting(E_ERROR);
        $checkRes = file_get_contents($url);
        if (!$checkRes) {
            Mage::getSingleton('install/session')->addError(
                __('Url "%s" is not accessible', $url)
            );
            throw new Exception('Check url error');
        }

        $checkRes = file_get_contents($surl);
        if (!$checkRes) {
            Mage::getSingleton('install/session')->addError(
                __('Url "%s" is not accessible', $surl)
            );
            throw new Exception('Check url error');
        }
        error_reporting($reporting_level);
        return $this;
    }

    public function setEncryptionKey($key)
    {
        if (! $key) {
            $key = md5(time());
        }
        $this->_encryptKey = $key;
        return $this;
    }

}
